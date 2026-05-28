<?php

namespace App\Http\Controllers;

use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentariosController extends Controller
{
    // Obtener comentarios aprobados
    public function getAll()
    {
        $comentarios = Comentario::join('usuarios', 'comentarios.usuario_id', '=', 'usuarios.id')
            ->select('comentarios.*', 'usuarios.nombre as usuario_nombre')
            ->where('comentarios.aprobado', true)
            ->orderBy('comentarios.fecha_creacion', 'desc')
            ->limit(50)
            ->get();

        return response()->json($comentarios);
    }

    // Crear comentario
    public function create(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string|min:5|max:500'
        ], [
            'contenido.min' => 'El comentario debe tener al menos 5 caracteres.',
            'contenido.max' => 'El comentario no puede exceder 500 caracteres.'
        ]);

        $comentario = Comentario::create([
            'usuario_id' => $request->user()->id,
            'contenido' => trim($request->contenido),
            'aprobado' => true
        ]);

        return response()->json([
            'message' => 'Comentario publicado exitosamente.',
            'id' => $comentario->id,
            'contenido' => $comentario->contenido,
            'usuario_nombre' => $request->user()->nombre,
            'fecha_creacion' => now()->toIso8601String()
        ], 201);
    }

    // Eliminar comentario
    public function delete(Request $request, $id)
    {
        $comentario = Comentario::find($id);

        if (!$comentario) {
            return response()->json(['error' => 'Comentario no encontrado.'], 404);
        }

        // Si no es admin y no es el dueño, rechazar
        if ($request->user()->rol !== 'admin' && $comentario->usuario_id !== $request->user()->id) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $comentario->delete();

        return response()->json(['message' => 'Comentario eliminado exitosamente.']);
    }
}
