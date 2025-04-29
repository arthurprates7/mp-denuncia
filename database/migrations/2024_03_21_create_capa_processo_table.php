<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('capa_processo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->constrained()->onDelete('cascade');
            $table->string('numero_processo');
            $table->date('data_distribuicao');
            $table->string('foro');
            $table->string('comarca');
            $table->string('juiz');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('capa_processo');
    }
}; 