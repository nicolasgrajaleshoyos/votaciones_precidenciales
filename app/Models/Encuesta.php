<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Encuesta extends Model
{
    protected $table = 'encuestas';

    protected $fillable = [
        'titulo',
        'tipo',
        'fuente',
        'fecha_realizacion',
        'margen_error',
        'muestra',
        'descripcion',
        'activa'
    ];

    public function resultados(): HasMany
    {
        return $this->hasMany(EncuestaResultado::class, 'encuesta_id');
    }
}
