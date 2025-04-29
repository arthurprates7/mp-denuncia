<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Processo extends Model
{
    protected $fillable = [
        'numero_cnj',
        'titulo',
        'caminho_arquivo',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function capa()
    {
        return $this->hasOne(CapaProcesso::class);
    }

    public function documentos()
    {
        return $this->hasMany(DocumentoProcesso::class);
    }
} 