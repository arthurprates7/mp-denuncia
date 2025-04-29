<?php

namespace App\Services;

use App\Models\Processo;
use App\Models\CapaProcesso;
use App\Models\DocumentoProcesso;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;

class ProcessadorPdf
{
    private $parser;
    private $embeddingService;

    public function __construct(EmbeddingService $embeddingService)
    {
        $this->parser = new Parser();
        $this->embeddingService = $embeddingService;
    }

    public function processar(Processo $processo)
    {
        $caminhoArquivo = Storage::disk('private')->path($processo->caminho_arquivo);
        
        // Verificar se o caminho existe e é um arquivo
        if (!file_exists($caminhoArquivo)) {
            throw new \Exception("O arquivo não existe no caminho: {$caminhoArquivo}");
        }
        
        if (is_dir($caminhoArquivo)) {
            throw new \Exception("O caminho aponta para um diretório, não para um arquivo: {$caminhoArquivo}");
        }

        $pdf = $this->parser->parseFile($caminhoArquivo);
        $texto = $pdf->getText();

        // Garantir que o texto esteja em UTF-8
        $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');

        // Processar capa
        $this->processarCapa($processo, $texto);

        // Processar documentos
        $this->processarDocumentos($processo, $texto);

        // Criar embeddings para o texto completo
        $this->embeddingService->criarEmbedding($processo, $texto);
    }

    private function processarCapa(Processo $processo, string $texto)
    {
        // Extrair informações da capa usando expressões regulares
        preg_match('/Número do Processo:\s*([^\n]+)/', $texto, $numeroProcesso);
        preg_match('/Data de Distribuição:\s*([^\n]+)/', $texto, $dataDistribuicao);
        preg_match('/Foro:\s*([^\n]+)/', $texto, $foro);
        preg_match('/Comarca:\s*([^\n]+)/', $texto, $comarca);
        preg_match('/Juiz:\s*([^\n]+)/', $texto, $juiz);

        // Converter a data do formato DD/MM/YYYY para YYYY-MM-DD
        $dataFormatada = null;
        if (!empty($dataDistribuicao[1])) {
            $dataObj = \DateTime::createFromFormat('d/m/Y', $dataDistribuicao[1]);
            if ($dataObj) {
                $dataFormatada = $dataObj->format('Y-m-d');
            }
        }

        CapaProcesso::create([
            'processo_id' => $processo->id,
            'numero_processo' => $numeroProcesso[1] ?? '',
            'data_distribuicao' => $dataFormatada,
            'foro' => $foro[1] ?? '',
            'comarca' => $comarca[1] ?? '',
            'juiz' => $juiz[1] ?? ''
        ]);
    }

    private function processarDocumentos(Processo $processo, string $texto)
    {
        // Dividir o texto em páginas
        $paginas = explode("\f", $texto);
        
        // Identificar tipos de documentos
        $tiposDocumentos = [
            'Interrogatório' => '/INTERROGATÓRIO/i',
            'Boletim de Ocorrência' => '/BOLETIM DE OCORRÊNCIA/i',
            'Documentos Diversos' => '/DOCUMENTOS DIVERSOS/i'
        ];

        $documentos = [];
        $documentoAtual = null;
        $conteudoAtual = '';

        foreach ($paginas as $indice => $pagina) {
            foreach ($tiposDocumentos as $tipo => $padrao) {
                if (preg_match($padrao, $pagina)) {
                    // Se já existe um documento sendo processado, salva ele
                    if ($documentoAtual !== null) {
                        $documentos[] = [
                            'tipo' => $documentoAtual,
                            'conteudo' => $conteudoAtual,
                            'pagina_inicial' => $documentos[count($documentos)-1]['pagina_inicial'] ?? $indice + 1,
                            'pagina_final' => $indice
                        ];
                    }
                    
                    // Inicia novo documento
                    $documentoAtual = $tipo;
                    $conteudoAtual = $pagina;
                    break;
                }
            }

            // Se não encontrou um novo tipo de documento e está processando um documento
            if ($documentoAtual !== null && !preg_match('/' . implode('|', array_values($tiposDocumentos)) . '/i', $pagina)) {
                $conteudoAtual .= $pagina;
            }
        }

        // Salva o último documento se existir
        if ($documentoAtual !== null) {
            $documentos[] = [
                'tipo' => $documentoAtual,
                'conteudo' => $conteudoAtual,
                'pagina_inicial' => $documentos[count($documentos)-1]['pagina_inicial'] ?? count($paginas),
                'pagina_final' => count($paginas)
            ];
        }

        // Salva os documentos no banco de dados
        foreach ($documentos as $doc) {
            DocumentoProcesso::create([
                'processo_id' => $processo->id,
                'tipo_documento' => $doc['tipo'],
                'pagina_inicial' => $doc['pagina_inicial'],
                'pagina_final' => $doc['pagina_final'],
                'texto' => mb_convert_encoding($doc['conteudo'], 'UTF-8', 'auto')
            ]);
        }
    }
} 