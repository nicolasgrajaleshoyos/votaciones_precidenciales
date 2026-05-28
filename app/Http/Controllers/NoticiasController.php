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
        $noticias = Noticia::where('destacada', true)
            ->orderBy('fecha_publicacion', 'desc')
            ->limit(5)
            ->get();

        return response()->json($noticias);
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
