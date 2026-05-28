<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Candidato;
use App\Models\User;
use App\Models\Voto;
use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use App\Models\TendenciaRedes;
use App\Models\Noticia;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $candidatos = Candidato::all();
        if ($candidatos->isEmpty()) return;

        // 1. Crear usuarios y votos simulados
        for ($i = 0; $i < 50; $i++) {
            $userId = \Illuminate\Support\Facades\DB::table('usuarios')->insertGetId([
                'nombre' => 'User ' . $i,
                'email' => 'user' . $i . '@example.com',
                'password_hash' => bcrypt('password'),
                'rol' => 'voter',
                'activo' => 1
            ]);

            // Voto aleatorio sesgado hacia los primeros candidatos
            $candidato = $candidatos->random();
            Voto::create([
                'usuario_id' => $userId,
                'candidato_id' => $candidato->id,
                'fecha_voto' => Carbon::now()->subMinutes(rand(1, 1000))
            ]);
        }

        // 2. Crear una encuesta reciente
        $encuesta = Encuesta::create([
            'titulo' => 'Gran Encuesta Nacional Invamer - Mayo 2026',
            'tipo' => 'primera_vuelta',
            'fuente' => 'Invamer',
            'fecha_realizacion' => Carbon::now()->subDays(2),
            'margen_error' => 2.5,
            'muestra' => 1200
        ]);

        $porcentajes = [25.5, 22.0, 18.5, 12.0, 10.0];
        foreach ($candidatos as $index => $candidato) {
            $porcentaje = $porcentajes[$index] ?? rand(1, 5);
            EncuestaResultado::create([
                'encuesta_id' => $encuesta->id,
                'candidato_id' => $candidato->id,
                'porcentaje' => $porcentaje
            ]);
        }

        // 3. Tendencias de Redes Sociales
        $plataformas = ['twitter', 'tiktok', 'instagram'];
        foreach ($candidatos as $candidato) {
            foreach ($plataformas as $plat) {
                TendenciaRedes::create([
                    'candidato_id' => $candidato->id,
                    'plataforma' => $plat,
                    'menciones' => rand(5000, 50000),
                    'sentimiento' => rand(-50, 80) / 100, // -0.5 a 0.8
                    'seguidores' => rand(100000, 2000000),
                    'interacciones' => rand(10000, 500000),
                    'fecha_registro' => Carbon::today()
                ]);
            }
        }

        // 4. Noticias recientes
        Noticia::create([
            'titulo' => 'Sube la tensión a días de las elecciones en Colombia',
            'contenido' => 'Los candidatos intensifican sus campañas...',
            'resumen' => 'Últimos movimientos de campaña marcan la agenda política.',
            'fuente' => 'El Tiempo',
            'categoria' => 'politica',
            'destacada' => true,
            'fecha_publicacion' => Carbon::now()->subHours(2)
        ]);

        Noticia::create([
            'titulo' => 'Nuevo debate presidencial muestra claros favoritos',
            'contenido' => 'En el debate organizado anoche...',
            'resumen' => 'El debate consolidó las tendencias que muestran las encuestas recientes.',
            'fuente' => 'Semana',
            'categoria' => 'debate',
            'destacada' => true,
            'fecha_publicacion' => Carbon::now()->subHours(5)
        ]);
    }
}
