<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\EncuestaResultado;
use Illuminate\Http\Request;

class EncuestasController extends Controller
{
    // Obtener todas las encuestas
    public function getAll(Request $request)
    {
        $tipo = $request->query('tipo');
        $activa = $request->query('activa');

        $query = Encuesta::query();

        if ($tipo) {
            $query->where('tipo', $tipo);
        }

        if (!is_null($activa)) {
            if (strtolower($activa) !== 'all') {
                $query->where('activa', filter_var($activa, FILTER_VALIDATE_BOOLEAN));
            }
        } else {
            // Por defecto solo mostrar encuestas activas en la app pública
            $query->where('activa', true);
        }

        $encuestas = $query->orderBy('fecha_realizacion', 'desc')->get();

        foreach ($encuestas as $enc) {
            $enc->resultados = EncuestaResultado::where('encuesta_id', $enc->id)
                ->join('candidatos', 'encuesta_resultados.candidato_id', '=', 'candidatos.id')
                ->select('encuesta_resultados.*', 'candidatos.nombre as candidato_nombre', 'candidatos.partido', 'candidatos.color_partido')
                ->orderBy('encuesta_resultados.porcentaje', 'desc')
                ->get();
        }

        return response()->json($encuestas);
    }

    // Obtener una encuesta por ID
    public function getById($id)
    {
        $encuesta = Encuesta::find($id);

        if (!$encuesta) {
            return response()->json(['error' => 'Encuesta no encontrada.'], 404);
        }

        $resultados = EncuestaResultado::where('encuesta_id', $id)
            ->join('candidatos', 'encuesta_resultados.candidato_id', '=', 'candidatos.id')
            ->select('encuesta_resultados.*', 'candidatos.nombre as candidato_nombre', 'candidatos.partido', 'candidatos.color_partido')
            ->orderBy('encuesta_resultados.porcentaje', 'desc')
            ->get();

        $encuestaArray = $encuesta->toArray();
        $encuestaArray['resultados'] = $resultados;

        return response()->json($encuestaArray);
    }

    // Crear encuesta (Admin)
    public function create(Request $request)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $request->validate([
            'titulo' => 'required|string',
            'tipo' => 'required|string|in:primera_vuelta,segunda_vuelta',
            'fuente' => 'nullable|string',
            'fecha_realizacion' => 'nullable|date',
            'margen_error' => 'nullable|numeric',
            'muestra' => 'nullable|integer',
            'descripcion' => 'nullable|string',
            'resultados' => 'nullable|array'
        ]);

        $encuesta = Encuesta::create($request->only([
            'titulo', 'tipo', 'fuente', 'fecha_realizacion', 'margen_error', 'muestra', 'descripcion'
        ]));

        if ($request->has('resultados') && is_array($request->resultados)) {
            foreach ($request->resultados as $r) {
                EncuestaResultado::create([
                    'encuesta_id' => $encuesta->id,
                    'candidato_id' => $r['candidato_id'],
                    'porcentaje' => $r['porcentaje']
                ]);
            }
        }

        return response()->json(['message' => 'Encuesta creada exitosamente.', 'id' => $encuesta->id], 201);
    }

    // Eliminar encuesta (Admin)
    public function delete(Request $request, $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $encuesta = Encuesta::find($id);

        if (!$encuesta) {
            return response()->json(['error' => 'Encuesta no encontrada.'], 404);
        }

        $encuesta->delete();

        return response()->json(['message' => 'Encuesta eliminada exitosamente.']);
    }
}
