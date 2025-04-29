<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapaProcesso extends Model
{
    protected $table = 'capa_processo';

    protected $fillable = [
        'processo_id',
        'numero_processo',
        'data_distribuicao',
        'foro',
        'comarca',
        'juiz'
    ];

    protected $casts = [
        'data_distribuicao' => 'date'
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }
} 