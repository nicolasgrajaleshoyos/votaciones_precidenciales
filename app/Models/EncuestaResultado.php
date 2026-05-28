<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EncuestaResultado extends Model
{
    protected $table = 'encuesta_resultados';

    protected $fillable = [
        'encuesta_id',
        'candidato_id',
        'porcentaje'
    ];

    public function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class, 'encuesta_id');
    }

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}
