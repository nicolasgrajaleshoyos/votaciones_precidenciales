<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidato;

class CandidatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $candidatos = [
            [
                'nombre' => 'Claudia López',
                'partido' => 'Alianza Verde',
                'tendencia' => 'centro-izquierda',
                'favorabilidad' => 45.2,
                'tendencia_redes' => 48.0,
                'crecimiento_semanal' => 1.5,
                'color_partido' => '#4CAF50'
            ],
            [
                'nombre' => 'Vicky Dávila',
                'partido' => 'Independiente / Derecha',
                'tendencia' => 'derecha',
                'favorabilidad' => 38.5,
                'tendencia_redes' => 60.0,
                'crecimiento_semanal' => 3.2,
                'color_partido' => '#2196F3'
            ],
            [
                'nombre' => 'Gustavo Bolívar',
                'partido' => 'Pacto Histórico',
                'tendencia' => 'izquierda',
                'favorabilidad' => 35.0,
                'tendencia_redes' => 55.0,
                'crecimiento_semanal' => -0.5,
                'color_partido' => '#E91E63'
            ],
            [
                'nombre' => 'Sergio Fajardo',
                'partido' => 'Compromiso Ciudadano',
                'tendencia' => 'centro',
                'favorabilidad' => 40.0,
                'tendencia_redes' => 20.0,
                'crecimiento_semanal' => 0.1,
                'color_partido' => '#009688'
            ],
            [
                'nombre' => 'Juan Daniel Oviedo',
                'partido' => 'Con Toda Bogotá',
                'tendencia' => 'centro-derecha',
                'favorabilidad' => 50.1,
                'tendencia_redes' => 45.0,
                'crecimiento_semanal' => 2.0,
                'color_partido' => '#FF9800'
            ]
        ];

        foreach ($candidatos as $candidato) {
            Candidato::create($candidato);
        }
    }
}
