<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('processo_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained()->onDelete('cascade');
            $table->vector('embedding', 1536); // 1536 é o tamanho do vetor para o modelo text-embedding-ada-002
            $table->text('texto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processo_embeddings');
    }
}; 