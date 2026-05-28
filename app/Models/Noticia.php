<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Noticia extends Model
{
    protected $table = 'noticias';

    protected $fillable = [
        'titulo',
        'contenido',
        'resumen',
        'imagen_url',
        'fuente',
        'autor',
        'categoria',
        'destacada',
        'fecha_publicacion'
    ];
}
