<?php

namespace App\Services;

use App\Models\Processo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{
    private const VECTOR_SIZE = 1536;
    private $openaiClient;
    private $cacheTtl;

    public function __construct()
    {
        $this->openaiClient = \OpenAI::client(config('services.openai.api_key'));
        $this->cacheTtl = config('services.embedding.cache_ttl', 3600); // 1 hora por padrão
    }

    private function arrayToPgVector(array $array): string
    {
        // Garantir que todos os valores sejam números
        $array = array_map(function($value) {
            return is_numeric($value) ? (float)$value : 0.0;
        }, $array);

        // Preencher o array com zeros até atingir o tamanho desejado
        $array = array_pad($array, self::VECTOR_SIZE, 0.0);
        // Limitar o array ao tamanho máximo
        $array = array_slice($array, 0, self::VECTOR_SIZE);
        
        // Converter para string no formato PostgreSQL
        return '[' . implode(',', $array) . ']';
    }

    public function gerarEmbedding(string $texto): array
    {
        $cacheKey = 'embedding:' . md5($texto);
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($texto) {
            try {
                $response = $this->openaiClient->embeddings()->create([
                    'model' => 'text-embedding-ada-002',
                    'input' => $texto
                ]);

                return $response->embeddings[0]->embedding;
            } catch (\Exception $e) {
                Log::error('Erro ao gerar embedding: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    public function criarEmbedding(Processo $processo, string $texto)
    {
        try {
            $embedding = $this->gerarEmbedding($texto);
            $embeddingVector = $this->arrayToPgVector($embedding);

            // Inserir o embedding no banco de dados
            DB::statement('
                INSERT INTO processo_embeddings (processo_id, embedding, texto, created_at, updated_at)
                VALUES (?, ?::vector, ?, NOW(), NOW())
            ', [$processo->id, $embeddingVector, $texto]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erro ao criar embedding: ' . $e->getMessage());
            return false;
        }
    }

    public function buscarSimilares(Processo $processo, string $consulta, int $limite = 5)
    {
        try {
            // Cache para a consulta + processo
            $cacheKey = 'similares:' . md5($consulta . $processo->id . $limite);
            
            return Cache::remember($cacheKey, $this->cacheTtl, function () use ($processo, $consulta, $limite) {
                $embeddingConsulta = $this->gerarEmbedding($consulta);
                $embeddingVector = $this->arrayToPgVector($embeddingConsulta);

                // Buscar textos similares usando pgvector com índice otimizado
                $resultados = DB::select('
                    SELECT texto, embedding <-> ?::vector as distancia
                    FROM processo_embeddings
                    WHERE processo_id = ?
                    AND embedding <-> ?::vector < 0.8  -- Limite de similaridade
                    ORDER BY distancia
                    LIMIT ?
                ', [$embeddingVector, $processo->id, $embeddingVector, $limite]);

                // Pré-carregar embeddings para consultas futuras
                foreach ($resultados as $resultado) {
                    $textoCacheKey = 'embedding:' . md5($resultado->texto);
                    if (!Cache::has($textoCacheKey)) {
                        Cache::put($textoCacheKey, $embeddingVector, $this->cacheTtl);
                    }
                }

                return $resultados;
            });
        } catch (\Exception $e) {
            Log::error('Erro ao buscar similares: ' . $e->getMessage());
            return [];
        }
    }

    public function salvarEmbedding(Processo $processo, string $texto, array $embedding)
    {
        try {
            DB::table('processo_embeddings')->insert([
                'processo_id' => $processo->id,
                'texto' => $texto,
                'embedding' => $this->arrayToPgVector($embedding),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao salvar embedding: ' . $e->getMessage());
            throw $e;
        }
    }
} 