<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use App\Models\TendenciaRedes;
use Illuminate\Http\Request;

class CandidatosController extends Controller
{
    // Obtener todos
    public function getAll()
    {
        $candidatos = Candidato::where('activo', true)
            ->withCount('votos as total_votos')
            ->orderBy('total_votos', 'desc')
            ->get();

        return response()->json($candidatos);
    }

    // Obtener por ID
    public function getById($id)
    {
        $candidato = Candidato::withCount('votos as total_votos')
            ->find($id);

        if (!$candidato) {
            return response()->json(['error' => 'Candidato no encontrado.'], 404);
        }

        // Obtener tendencias de redes
        $redes = TendenciaRedes::where('candidato_id', $id)
            ->orderBy('fecha_registro', 'desc')
            ->limit(5)
            ->get();

        $candidatoArray = $candidato->toArray();
        $candidatoArray['redes_sociales'] = $redes;

        return response()->json($candidatoArray);
    }

    // Actualizar (Admin)
    public function update(Request $request, $id)
    {
        if ($request->user()->rol !== 'admin') {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $candidato = Candidato::find($id);

        if (!$candidato) {
            return response()->json(['error' => 'Candidato no encontrado.'], 404);
        }

        $candidato->update(array_filter([
            'nombre' => $request->nombre,
            'partido' => $request->partido,
            'biografia' => $request->biografia,
            'propuestas' => $request->propuestas,
            'favorabilidad' => $request->favorabilidad,
            'tendencia_redes' => $request->tendencia_redes,
            'crecimiento_semanal' => $request->crecimiento_semanal,
            'color_partido' => $request->color_partido,
        ], function ($value) {
            return $value !== null;
        }));

        return response()->json(['message' => 'Candidato actualizado exitosamente.']);
    }
}
