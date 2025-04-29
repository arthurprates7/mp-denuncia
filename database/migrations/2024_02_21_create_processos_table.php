<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_cnj')->unique();
            $table->string('titulo');
            $table->string('caminho_arquivo');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('processos');
    }
}; 