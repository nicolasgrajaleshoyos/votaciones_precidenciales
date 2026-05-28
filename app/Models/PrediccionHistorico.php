<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrediccionHistorico extends Model
{
    protected $table = 'predicciones_historico';

    protected $fillable = [
        'candidato_id',
        'probabilidad',
        'intencion_voto',
        'favorabilidad',
        'redes_score',
        'crecimiento'
    ];

    public function candidato(): BelongsTo
    {
        return $this->belongsTo(Candidato::class, 'candidato_id');
    }
}
