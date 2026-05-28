<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidato extends Model
{
    protected $table = 'candidatos';

    protected $fillable = [
        'nombre',
        'formula_vice',
        'partido',
        'tendencia',
        'foto_url',
        'biografia',
        'propuestas',
        'favorabilidad',
        'tendencia_redes',
        'crecimiento_semanal',
        'color_partido',
        'activo'
    ];

    public function votos(): HasMany
    {
        return $this->hasMany(Voto::class, 'candidato_id');
    }

    public function tendenciasRedes(): HasMany
    {
        return $this->hasMany(TendenciaRedes::class, 'candidato_id');
    }

    public function encuestaResultados(): HasMany
    {
        return $this->hasMany(EncuestaResultado::class, 'candidato_id');
    }
}
