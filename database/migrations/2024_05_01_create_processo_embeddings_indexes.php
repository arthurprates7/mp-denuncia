<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Índice para busca por similaridade usando ivfflat
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_processo_embeddings_embedding 
            ON processo_embeddings 
            USING ivfflat (embedding vector_cosine_ops) 
            WITH (lists = 100)
        ');

        // Índice para busca por processo_id
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_processo_embeddings_processo_id 
            ON processo_embeddings (processo_id)
        ');

        // Índice para texto para buscas full-text
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_processo_embeddings_texto 
            ON processo_embeddings USING gin (to_tsvector(\'portuguese\', texto))
        ');

        // Índice para otimizar ordenação por data
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_processo_embeddings_created_at 
            ON processo_embeddings (created_at)
        ');

        // Índice para otimizar consultas por processo e data
        DB::statement('
            CREATE INDEX IF NOT EXISTS idx_processo_embeddings_processo_created 
            ON processo_embeddings (processo_id, created_at)
        ');
    }

    public function down()
    {
        DB::statement('DROP INDEX IF EXISTS idx_processo_embeddings_embedding');
        DB::statement('DROP INDEX IF EXISTS idx_processo_embeddings_processo_id');
        DB::statement('DROP INDEX IF EXISTS idx_processo_embeddings_texto');
        DB::statement('DROP INDEX IF EXISTS idx_processo_embeddings_created_at');
        DB::statement('DROP INDEX IF EXISTS idx_processo_embeddings_processo_created');
    }
}; 