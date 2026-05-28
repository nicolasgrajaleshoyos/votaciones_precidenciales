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
        $this->checkAndSimulatePolls();

        $tipo = $request->query('tipo');

        $query = Encuesta::where('activa', true);

        if ($tipo) {
            $query->where('tipo', $tipo);
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

    private function checkAndSimulatePolls()
    {
        try {
            $lastPoll = Encuesta::orderBy('fecha_realizacion', 'desc')->first();
            if (!$lastPoll || \Carbon\Carbon::parse($lastPoll->fecha_realizacion)->diffInSeconds(now()) > 60 || Encuesta::count() < 8) {
                $this->simulateNewPoll();
            }
        } catch (\Exception $e) {
            \Log::error('Error simulating polls: ' . $e->getMessage());
        }
    }

    private function simulateNewPoll()
    {
        if (Encuesta::count() >= 15) {
            $oldest = Encuesta::orderBy('fecha_realizacion', 'asc')->first();
            if ($oldest) {
                $oldest->delete();
            }
        }

        $firms = ['Invamer-Gallup', 'Datexco', 'Centro Nacional de Consultoría', 'Guarumo-EcoAnalítica', 'Cifras y Conceptos', 'Yanhaas'];
        $firm = $firms[array_rand($firms)];
        
        $months = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo'];
        $month = $months[array_rand($months)];
        
        $title = "Medición de Intención de Voto — " . $firm . " (" . $month . " 2026)";
        $desc = "Estudio nacional de opinión para la primera vuelta presidencial del 31 de mayo de 2026. Muestra representativa a nivel país.";

        $encuesta = Encuesta::create([
            'titulo' => $title,
            'tipo' => 'primera_vuelta',
            'fuente' => $firm,
            'fecha_realizacion' => now()->format('Y-m-d'),
            'margen_error' => number_format(2.2 + (rand(0, 15) / 10), 2),
            'muestra' => rand(15, 38) * 100,
            'descripcion' => $desc,
            'activa' => true
        ]);

        $candidates = \App\Models\Candidato::all();
        
        $weights = [
            1 => rand(250, 310),
            2 => rand(160, 210),
            3 => rand(110, 160),
            4 => rand(80, 120),
            5 => rand(90, 130),
            6 => rand(40, 60),
            7 => rand(30, 50),
            8 => rand(25, 45),
            9 => rand(25, 40),
            10 => rand(10, 25),
            11 => rand(20, 35),
            12 => rand(5, 15),
            13 => rand(5, 15),
        ];

        $totalWeight = array_sum($weights);
        $remaining = 100.0;

        foreach ($candidates as $index => $c) {
            $w = $weights[$c->id] ?? 10;
            $percent = round(($w / $totalWeight) * 100, 2);
            
            if ($index === count($candidates) - 1) {
                $percent = round($remaining, 2);
            } else {
                $remaining -= $percent;
            }

            EncuestaResultado::create([
                'encuesta_id' => $encuesta->id,
                'candidato_id' => $c->id,
                'porcentaje' => $percent
            ]);
        }
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
