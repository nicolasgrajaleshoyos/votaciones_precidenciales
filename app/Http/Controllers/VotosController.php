<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotosController extends Controller
{
    // Emitir voto
    public function votar(Request $request)
    {
        $request->validate([
            'candidato_id' => 'required|exists:candidatos,id'
        ]);

        $usuario_id = $request->user()->id;

        // Verificar si ya votó
        $existingVote = Voto::where('usuario_id', $usuario_id)->first();
        if ($existingVote) {
            return response()->json(['error' => 'Ya has emitido tu voto. Solo se permite un voto por usuario.'], 400);
        }

        $candidato = Candidato::find($request->candidato_id);
        if (!$candidato || !$candidato->activo) {
            return response()->json(['error' => 'Candidato no encontrado.'], 404);
        }

        // Registrar voto
        $ip = $request->ip();
        Voto::create([
            'usuario_id' => $usuario_id,
            'candidato_id' => $request->candidato_id,
            'ip_address' => $ip
        ]);

        return response()->json([
            'message' => 'Voto registrado exitosamente por ' . $candidato->nombre,
            'candidato' => $candidato->nombre
        ], 201);
    }

    // Obtener resultados en tiempo real
    public function getResultados()
    {
        $totalVotos = Voto::count();

        // Obtener votos por candidato
        $resultados = Candidato::where('activo', true)
            ->withCount('votos')
            ->get()
            ->map(function ($candidato) use ($totalVotos) {
                $divisor = $totalVotos ?: 1;
                return [
                    'id' => $candidato->id,
                    'nombre' => $candidato->nombre,
                    'partido' => $candidato->partido,
                    'tendencia' => $candidato->tendencia,
                    'color_partido' => $candidato->color_partido,
                    'foto_url' => $candidato->foto_url,
                    'votos' => $candidato->votos_count,
                    'porcentaje' => round(($candidato->votos_count * 100.0) / $divisor, 2)
                ];
            })
            ->sortByDesc('votos')
            ->values();

        $masVotado = $resultados->first() ?: null;

        return response()->json([
            'total_votos' => $totalVotos,
            'resultados' => $resultados,
            'mas_votado' => $masVotado,
            'ultima_actualizacion' => now()->toIso8601String()
        ]);
    }

    // Verificar si el usuario ya votó
    public function verificarVoto(Request $request)
    {
        $usuario_id = $request->user()->id;

        $voto = Voto::with('candidato')
            ->where('usuario_id', $usuario_id)
            ->first();

        return response()->json([
            'ya_voto' => $voto !== null,
            'voto' => $voto ? [
                'candidato_id' => $voto->candidato_id,
                'candidato_nombre' => $voto->candidato->nombre,
                'fecha_voto' => $voto->fecha_voto
            ] : null
        ]);
    }
}
