<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TendenciaRedes extends Model
{
    protected $table = 'tendencias_redes';

    protected $fillable = [
        'candidato_id',
        'plataforma',
        'menciones',
        'sentimiento',
        'seguidores',
        'interacciones',
        'hashtag_principal',
        'fecha_registro'
    ];

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}
