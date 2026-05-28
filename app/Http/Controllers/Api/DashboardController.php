<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Candidato;
use App\Services\Prediction\PredictionEngine;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    private $predictionEngine;

    public function __construct(PredictionEngine $predictionEngine)
    {
        $this->predictionEngine = $predictionEngine;
    }

    /**
     * Endpoint to fetch the current election dashboard data
     */
    public function getDashboardData(): JsonResponse
    {
        $candidatos = Candidato::where('activo', true)->get();
        
        $predictions = [];
        $mostLikely = null;
        $highestProb = 0;

        foreach ($candidatos as $candidato) {
            $prob = $this->predictionEngine->calculateFinalProbability($candidato);
            
            $candidatoData = [
                'id' => $candidato->id,
                'nombre' => $candidato->nombre,
                'partido' => $candidato->partido,
                'color' => $candidato->color_partido,
                'tendencia_semanal' => $candidato->crecimiento_semanal > 0 ? 'up' : 'down',
                'probabilidad' => $prob,
                'favorabilidad' => $candidato->favorabilidad
            ];

            $predictions[] = $candidatoData;

            if ($prob > $highestProb) {
                $highestProb = $prob;
                $mostLikely = $candidatoData;
            }
        }

        // Ordenar de mayor a menor probabilidad
        usort($predictions, function($a, $b) {
            return $b['probabilidad'] <=> $a['probabilidad'];
        });

        return response()->json([
            'status' => 'success',
            'data' => [
                'most_likely_candidate' => $mostLikely,
                'ranking' => $predictions,
                'total_candidates' => count($candidatos),
                'total_polls_analyzed' => 12, // Simulamos 12 encuestas scrapeadas
                'last_update' => now()->toIso8601String()
            ]
        ]);
    }
}
