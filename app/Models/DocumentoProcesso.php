<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoProcesso extends Model
{
    protected $table = 'documentos_processo';

    protected $fillable = [
        'processo_id',
        'tipo_documento',
        'pagina_inicial',
        'pagina_final',
        'texto'
    ];

    public function processo()
    {
        return $this->belongsTo(Processo::class);
    }
} 