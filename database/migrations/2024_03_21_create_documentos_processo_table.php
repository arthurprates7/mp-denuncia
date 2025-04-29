<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('documentos_processo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained()->onDelete('cascade');
            $table->string('tipo_documento');
            $table->integer('pagina_inicial');
            $table->integer('pagina_final');
            $table->text('texto');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('documentos_processo');
    }
}; 