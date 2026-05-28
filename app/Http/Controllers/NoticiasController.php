<?php

namespace App\Http\Controllers;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiasController extends Controller
{
    // Obtener todas las noticias
    public function getAll(Request $request)
    {
        $categoria = $request->query('categoria');
        $limit = $request->query('limit');

        $realNews = $this->fetchRealRSSNews();

        if (count($realNews) > 0) {
            if ($categoria && $categoria !== 'todas') {
                $realNews = array_values(array_filter($realNews, function($n) use ($categoria) {
                    return $n['categoria'] === $categoria;
                }));
            }
            if ($limit) {
                $realNews = array_slice($realNews, 0, intval($limit));
            }
            return response()->json($realNews);
        }

        // Fallback a base de datos si falla el lector de noticias reales (RSS)
        $query = Noticia::query();
        if ($categoria && $categoria !== 'todas') {
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
        $realNews = $this->fetchRealRSSNews();

        if (count($realNews) > 0) {
            $destacadas = array_values(array_filter($realNews, function($n) {
                return isset($n['destacada']) && $n['destacada'] === true;
            }));
            return response()->json(array_slice($destacadas, 0, 5));
        }

        // Fallback a base de datos si falla el lector de noticias reales (RSS)
        $noticias = Noticia::where('destacada', true)
            ->orderBy('fecha_publicacion', 'desc')
            ->limit(5)
            ->get();

        return response()->json($noticias);
    }

    private function fetchRealRSSNews()
    {
        $feeds = [
            'El Espectador' => 'https://www.elespectador.com/arc/outboundfeeds/rss/category/politica/?outputType=xml',
            'El Tiempo' => 'https://www.eltiempo.com/rss/politica.xml'
        ];

        $allNews = [];
        $idCounter = 9000;

        foreach ($feeds as $source => $url) {
            try {
                $ctx = stream_context_create([
                    'http' => ['timeout' => 3, 'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)']
                ]);
                $xmlContent = @file_get_contents($url, false, $ctx);
                if (!$xmlContent) continue;
                
                $xml = @simplexml_load_string($xmlContent);
                if (!$xml || !isset($xml->channel->item)) continue;

                foreach ($xml->channel->item as $item) {
                    $titulo = (string)$item->title;
                    $link = (string)$item->link;
                    $description = strip_tags((string)$item->description);
                    
                    if (empty($description)) {
                        $description = "Noticia de actualidad política colombiana de última hora.";
                    }
                    
                    $pubDate = (string)$item->pubDate;
                    
                    $date = now();
                    try {
                        $date = \Carbon\Carbon::parse($pubDate)->timezone('America/Bogota');
                    } catch (\Exception $e) {}

                    $contentLower = mb_strtolower($titulo . ' ' . $description);
                    $categoria = 'politica';
                    if (str_contains($contentLower, 'encuesta') || str_contains($contentLower, 'sondeo') || str_contains($contentLower, 'invamer') || str_contains($contentLower, 'datexco') || str_contains($contentLower, 'cnc')) {
                        $categoria = 'encuestas';
                    } elseif (str_contains($contentLower, 'debate') || str_contains($contentLower, 'foro') || str_contains($contentLower, 'disputa')) {
                        $categoria = 'debate';
                    } elseif (str_contains($contentLower, 'reforma') || str_contains($contentLower, 'dólar') || str_contains($contentLower, 'tasa') || str_contains($contentLower, 'impuesto') || str_contains($contentLower, 'economía') || str_contains($contentLower, 'hacienda')) {
                        $categoria = 'economia';
                    } elseif (str_contains($contentLower, 'protesta') || str_contains($contentLower, 'marcha') || str_contains($contentLower, 'social') || str_contains($contentLower, 'salud') || str_contains($contentLower, 'pension') || str_contains($contentLower, 'docente') || str_contains($contentLower, 'sindicato')) {
                        $categoria = 'social';
                    }

                    $allNews[] = [
                        'id' => $idCounter++,
                        'titulo' => $titulo,
                        'contenido' => $description . " Lee la noticia completa en " . $link,
                        'resumen' => mb_strlen($description) > 180 ? mb_substr($description, 0, 180) . '...' : $description,
                        'imagen_url' => null,
                        'fuente' => $source,
                        'autor' => $source . ' Redacción',
                        'categoria' => $categoria,
                        'destacada' => false,
                        'fecha_publicacion' => $date->toDateTimeString()
                    ];
                }
            } catch (\Exception $e) {
                \Log::error("Error leyendo feed RSS político: " . $e->getMessage());
            }
        }

        if (count($allNews) > 0) {
            usort($allNews, function($a, $b) {
                return strtotime($b['fecha_publicacion']) - strtotime($a['fecha_publicacion']);
            });

            for ($i = 0; $i < min(4, count($allNews)); $i++) {
                $allNews[$i]['destacada'] = true;
            }
        }

        return $allNews;
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
