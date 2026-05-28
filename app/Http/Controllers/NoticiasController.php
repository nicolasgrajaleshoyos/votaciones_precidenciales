<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    // Obtener todas las noticias
    public function getAll(Request $request)
    {
        $this->checkAndSimulateNews();

        $categoria = $request->query('categoria');
        $limit = $request->query('limit');

        $query = Noticia::query();

        if ($categoria) {
            $query->where('categoria', $categoria);
        }

        $query->orderBy('fecha_publicacion', 'desc');

        if ($limit) {
            $query->limit(intval($limit));
        }

        return response()->json($query->get());
    }

    // Obtener destacadas
    public function getDestacadas()
    {
        $this->checkAndSimulateNews();

        $noticias = Noticia::where('destacada', true)
            ->orderBy('fecha_publicacion', 'desc')
            ->limit(5)
            ->get();

        return response()->json($noticias);
    }

    private function checkAndSimulateNews()
    {
        try {
            $lastNews = Noticia::orderBy('fecha_publicacion', 'desc')->first();
            if (!$lastNews || \Carbon\Carbon::parse($lastNews->fecha_publicacion)->diffInSeconds(now()) > 60 || Noticia::count() < 18) {
                $this->simulateNewNews();
            }
        } catch (\Exception $e) {
            \Log::error('Error simulating news: ' . $e->getMessage());
        }
    }

    private function simulateNewNews()
    {
        if (Noticia::count() >= 30) {
            $oldest = Noticia::orderBy('fecha_publicacion', 'asc')->first();
            if ($oldest) {
                $oldest->delete();
            }
        }

        $candidatos = [
            'Iván Cepeda', 'Paloma Valencia', 'Abelardo de la Espriella', 
            'Sergio Fajardo', 'Claudia López', 'Roy Barreras', 'Carlos Caicedo', 
            'Luis Gilberto Murillo', 'Mauricio Lizcano', 'Santiago Botero', 
            'Miguel Uribe Londoño', 'Sondra Macollins', 'Gustavo Matamoros'
        ];

        $templates = [
            [
                'titulo' => 'CAMP_NAME lidera agenda de propuestas en departamento de DEP_NAME',
                'contenido' => 'El candidato CAMP_NAME visitó la capital del departamento de DEP_NAME hoy para presentar su visión de gobierno para el periodo 2026-2030. Ante una multitud congregada en la plaza principal, expuso sus compromisos de infraestructura, empleo juvenil y desarrollo de la economía regional. Los líderes del sector privado manifestaron su interés en abrir mesas de diálogo.',
                'resumen' => 'CAMP_NAME presentó sus propuestas clave de reactivación en DEP_NAME.',
                'categoria' => 'politica',
                'fuente' => 'El Espectador',
                'autor' => 'Corresponsal Regional'
            ],
            [
                'titulo' => 'Debate sobre transición energética: CAMP_NAME defiende su postura',
                'contenido' => 'Durante un foro organizado por universidades públicas, CAMP_NAME se pronunció sobre el futuro de los recursos minero-energéticos de Colombia. Insistió en que es necesario avanzar con responsabilidad fiscal y social para no afectar las finanzas del país. Sus oponentes criticaron algunos apartes, pero el público elogió la precisión de los datos presentados.',
                'resumen' => 'El candidato CAMP_NAME debatió sobre la transición ecológica en un foro universitario.',
                'categoria' => 'debate',
                'fuente' => 'Portafolio',
                'autor' => 'Redacción Economía'
            ],
            [
                'titulo' => 'Última encuesta en DEP_NAME muestra repunte de CAMP_NAME',
                'contenido' => 'Un nuevo estudio de opinión pública en el departamento de DEP_NAME revela un crecimiento notable en la favorabilidad de CAMP_NAME. Las cifras muestran un incremento de 3.5 puntos respecto al mes anterior, consolidándose como una de las opciones más fuertes en esta zona del país. Los analistas locales atribuyen este crecimiento a su reciente gira de propuestas.',
                'resumen' => 'Favorabilidad de CAMP_NAME registra un repunte de 3.5% en encuestas de DEP_NAME.',
                'categoria' => 'encuestas',
                'fuente' => 'Semana',
                'autor' => 'Diana Rincón'
            ],
            [
                'titulo' => 'CAMP_NAME propone reformas clave para impulsar el desarrollo agrario',
                'contenido' => 'El candidato presidencial CAMP_NAME lanzó hoy su plan de desarrollo rural integral. El proyecto plantea subsidios directos a pequeños productores, créditos blandos con la banca estatal y la modernización de los canales de distribución nacional de alimentos. Su objetivo es convertir a Colombia en despensa alimentaria de la región.',
                'resumen' => 'Reforma agraria y subsidios a productores: el plan rural de CAMP_NAME.',
                'categoria' => 'economia',
                'fuente' => 'La República',
                'autor' => 'Carlos Gómez'
            ],
            [
                'titulo' => 'Fuerte debate en redes sociales por las últimas declaraciones de CAMP_NAME',
                'contenido' => 'Las opiniones compartidas por CAMP_NAME en sus canales digitales generaron miles de comentarios y reacciones encontradas hoy. Militantes y críticos debatieron sobre la viabilidad de sus reformas propuestas en seguridad social y educación. Su equipo de campaña celebró la alta tasa de interacciones y engagement en las plataformas de video.',
                'resumen' => 'Declaraciones de CAMP_NAME sobre reformas abren debate viral en redes sociales.',
                'categoria' => 'social',
                'fuente' => 'La Silla Vacía',
                'autor' => 'Mateo Silva'
            ]
        ];

        $departamentos = ['Antioquia', 'Valle del Cauca', 'Atlántico', 'Santander', 'Cundinamarca', 'Bolívar', 'Risaralda', 'Huila', 'Nariño', 'Tolima'];

        $camp = $candidatos[array_rand($candidatos)];
        $dep = $departamentos[array_rand($departamentos)];
        $tpl = $templates[array_rand($templates)];

        $titulo = str_replace(['CAMP_NAME', 'DEP_NAME'], [$camp, $dep], $tpl['titulo']);
        $contenido = str_replace(['CAMP_NAME', 'DEP_NAME'], [$camp, $dep], $tpl['contenido']);
        $resumen = str_replace(['CAMP_NAME', 'DEP_NAME'], [$camp, $dep], $tpl['resumen']);

        Noticia::create([
            'titulo' => $titulo,
            'contenido' => $contenido,
            'resumen' => $resumen,
            'categoria' => $tpl['categoria'],
            'fuente' => $tpl['fuente'],
            'autor' => $tpl['autor'],
            'destacada' => (rand(0, 10) > 6),
            'fecha_publicacion' => now()
        ]);
    }

    // Crear noticia (Admin)
    public function create(Request $request)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $request->validate([
            'titulo' => 'required|string|max:300',
            'contenido' => 'required|string',
            'resumen' => 'nullable|string|max:500',
            'imagen_url' => 'nullable|string|max:500',
            'fuente' => 'nullable|string|max:200',
            'autor' => 'nullable|string|max:150',
            'categoria' => 'nullable|string',
            'destacada' => 'nullable|boolean'
        ]);

        $noticia = Noticia::create([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'resumen' => $request->resumen,
            'imagen_url' => $request->imagen_url,
            'fuente' => $request->fuente,
            'autor' => $request->autor,
            'categoria' => $request->categoria ?? 'politica',
            'destacada' => $request->destacada ?? false,
        ]);

        return response()->json(['message' => 'Noticia creada exitosamente.', 'id' => $noticia->id], 201);
    }

    // Actualizar noticia (Admin)
    public function update(Request $request, $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['error' => 'Noticia no encontrada.'], 404);
        }

        $noticia->update(array_filter([
            'titulo' => $request->titulo,
            'contenido' => $request->contenido,
            'resumen' => $request->resumen,
            'imagen_url' => $request->imagen_url,
            'fuente' => $request->fuente,
            'autor' => $request->autor,
            'categoria' => $request->categoria,
            'destacada' => $request->has('destacada') ? $request->destacada : null,
        ], function ($value) {
            return $value !== null;
        }));

        return response()->json(['message' => 'Noticia actualizada exitosamente.']);
    }

    // Eliminar noticia (Admin)
    public function delete(Request $request, $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $noticia = Noticia::find($id);

        if (!$noticia) {
            return response()->json(['error' => 'Noticia no encontrada.'], 404);
        }

        $noticia->delete();

        return response()->json(['message' => 'Noticia eliminada exitosamente.']);
    }
}
