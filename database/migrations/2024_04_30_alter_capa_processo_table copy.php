<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('capa_processo', function (Blueprint $table) {
            $table->date('data_distribuicao')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('capa_processo', function (Blueprint $table) {
            $table->date('data_distribuicao')->nullable(false)->change();
        });
    }
}; 