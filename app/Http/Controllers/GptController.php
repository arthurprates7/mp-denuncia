<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Services\EmbeddingService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Cache;

class GptController extends Controller
{
    private $embeddingService;
    private $openaiClient;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->embeddingService = $embeddingService;
        $this->openaiClient = \OpenAI::client(config('services.openai.api_key'));
    }

    public function getPdf(Request $request)
    {
        // Dados que serão enviados para a view do PDF
        $data = [
            'titulo' => 'Denúncia',
            'conteudo' => [
                'quota' => $request->query('quota', ''),
                'partes' => $request->query('partes', ''),
                'fatos' => $request->query('fatos', '')
            ],
            'data' => date('d/m/Y'),
        ];

        // Gerar o PDF com a view 'pdf.denuncia' e os dados
        $pdf = Pdf::loadView('pdf.denuncia', $data);

        // Retornar o PDF como resposta no navegador
        return $pdf->stream('denuncia.pdf');
    }

    public function streamQuota(Request $request)
    {
        try {
            $request->validate([
                'instrucoes' => 'required|string',
                'numero_cnj' => 'required|string'
            ]);

            // Buscar processo com cache
            $processo = Cache::remember("processo_{$request->numero_cnj}", 3600, function () use ($request) {
                return Processo::where('numero_cnj', $request->numero_cnj)->firstOrFail();
            });
            
            // Usar cache para os embeddings
            $cacheKey = "processo_embeddings_{$processo->id}";
            $textosSimilares = Cache::remember($cacheKey, now()->addHours(24), function () use ($processo, $request) {
                return $this->embeddingService->buscarSimilares($processo, $request->instrucoes, 3);
            });
            
            // Construir o prompt com contexto
            $prompt = "Processo: {$processo->numero_cnj}\n";
            $prompt .= "Título: {$processo->titulo}\n\n";
            $prompt .= "Contexto relevante:\n";
            foreach ($textosSimilares as $texto) {
                $prompt .= "- {$texto->texto}\n";
            }
            $prompt .= "\nInstruções: {$request->instrucoes}\n";
            $prompt .= "Por favor, analise o contexto e forneça uma resposta detalhada sobre a quota da denúncia desse processo vinculado.";

            // Fazer a chamada para a OpenAI
            $response = $this->openaiClient->chat()->createStreamed([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente jurídico especializado em análise de processos e vai me retornar a quota da denúncia desse processo vinculado.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'stream' => true
            ]);

            // Retornar a resposta em streaming
            return response()->stream(function () use ($response) {
                foreach ($response as $chunk) {
                    if (isset($chunk->choices[0]->delta->content)) {
                        $content = $chunk->choices[0]->delta->content;
                        if (!empty($content)) {
                            echo "data: " . json_encode(['text' => $content]) . "\n\n";
                            flush();
                        }
                    }
                }
                
                // Envia o sinal de fim
                echo "data: " . json_encode(['done' => true]) . "\n\n";
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Connection' => 'keep-alive'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function streamPartes(Request $request)
    {
        try {
            $request->validate([
                'instrucoes' => 'required|string',
                'numero_cnj' => 'required|string'
            ]);

            $processo = Processo::where('numero_cnj', $request->numero_cnj)->firstOrFail();
            
            // Buscar textos similares usando embeddings
            $textosSimilares = $this->embeddingService->buscarSimilares($processo, $request->instrucoes, 3);
            
            // Construir o prompt com contexto
            $prompt = "Processo: {$processo->numero_cnj}\n";
            $prompt .= "Título: {$processo->titulo}\n\n";
            $prompt .= "Contexto relevante sobre as partes:\n";
            foreach ($textosSimilares as $texto) {
                $prompt .= "- {$texto->texto}\n";
            }
            $prompt .= "\nInstruções: {$request->instrucoes}\n";
            $prompt .= "Por favor, analise o contexto e forneça uma resposta detalhada sobre as partes envolvidas no processo, seguindo EXATAMENTE este formato:\n\n";
            $prompt .= "1. Vítima/Autora/Querelante:\n";
            $prompt .= "   - Nome completo:\n";
            $prompt .= "   - Documentos apresentados:\n";
            $prompt .= "   - Endereço:\n";
            $prompt .= "   - Narração do fato:\n\n";
            $prompt .= "2. Acusado/Réu/Suspeito:\n";
            $prompt .= "   - Nome completo:\n";
            $prompt .= "   - Documentos apresentados:\n";
            $prompt .= "   - Endereço:\n";
            $prompt .= "   - Características pessoais:\n";
            $prompt .= "   - Defesa apresentada:\n\n";
            $prompt .= "3. Autoridades/Órgãos Envolvidos:\n";
            $prompt .= "   - Juiz:\n";
            $prompt .= "   - Autor Policial:\n";
            $prompt .= "   - Peritos:\n\n";
            $prompt .= "4. Testemunhas:\n";
            $prompt .= "   - Nome e qualificação:\n\n";
            $prompt .= "5. Observações:\n";
            $prompt .= "   - Informações adicionais relevantes\n\n";
            $prompt .= "6. Dúvidas ou Lacunas:\n";
            $prompt .= "   - Informações não encontradas ou incompletas";

            // Fazer a chamada para a OpenAI
            $response = $this->openaiClient->chat()->createStreamed([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente jurídico especializado em análise de processos. Sua resposta deve seguir EXATAMENTE o formato solicitado, preenchendo todos os campos mesmo que com "não informado" quando não houver dados.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'stream' => true,
                'temperature' => 0.3,
                'max_tokens' => 2000
            ]);

            // Retornar a resposta em streaming
            return response()->stream(function () use ($response) {
                foreach ($response as $chunk) {
                    if (isset($chunk->choices[0]->delta->content)) {
                        $content = $chunk->choices[0]->delta->content;
                        if (!empty($content)) {
                            echo "data: " . json_encode(['text' => $content]) . "\n\n";
                            flush();
                        }
                    }
                }
                
                // Envia o sinal de fim
                echo "data: " . json_encode(['done' => true]) . "\n\n";
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Connection' => 'keep-alive'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function streamFatos(Request $request)
    {
        try {
            $request->validate([
                'instrucoes' => 'required|string',
                'numero_cnj' => 'required|string'
            ]);

            $processo = Processo::where('numero_cnj', $request->numero_cnj)->firstOrFail();
            
            // Buscar textos similares usando embeddings
            $textosSimilares = $this->embeddingService->buscarSimilares($processo, $request->instrucoes, 3);
            
            // Construir o prompt com contexto
            $prompt = "Processo: {$processo->numero_cnj}\n";
            $prompt .= "Título: {$processo->titulo}\n\n";
            $prompt .= "Contexto relevante sobre os fatos:\n";
            foreach ($textosSimilares as $texto) {
                $prompt .= "- {$texto->texto}\n";
            }
            $prompt .= "\nInstruções: {$request->instrucoes}\n";
            $prompt .= "Por favor, analise o contexto e forneça uma resposta detalhada sobre os fatos relevantes do processo.";

            // Fazer a chamada para a OpenAI
            $response = $this->openaiClient->chat()->createStreamed([
                'model' => 'gpt-4',
                'messages' => [
                    ['role' => 'system', 'content' => 'Você é um assistente jurídico especializado em análise de processos, com foco em identificar e descrever os fatos relevantes.'],
                    ['role' => 'user', 'content' => $prompt]
                ],
                'stream' => true,
                'temperature' => 0.7,
                'max_tokens' => 2000
            ]);

            // Retornar a resposta em streaming
            return response()->stream(function () use ($response) {
                foreach ($response as $chunk) {
                    if (isset($chunk->choices[0]->delta->content)) {
                        $content = $chunk->choices[0]->delta->content;
                        if (!empty($content)) {
                            echo "data: " . json_encode(['text' => $content]) . "\n\n";
                            flush();
                        }
                    }
                }
                
                // Envia o sinal de fim
                echo "data: " . json_encode(['done' => true]) . "\n\n";
                flush();
            }, 200, [
                'Content-Type' => 'text/event-stream',
                'Cache-Control' => 'no-cache',
                'X-Accel-Buffering' => 'no',
                'Connection' => 'keep-alive'
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
