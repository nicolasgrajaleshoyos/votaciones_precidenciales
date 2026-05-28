<?php

namespace App\Services\Prediction;

use App\Models\Candidato; // Adaptado a la BD actual
use Illuminate\Support\Facades\Log;

class PredictionEngine
{
    /**
     * Calculates the final probability for a candidate based on the exact formula:
     * (Encuestas verificadas * 0.60) + (Promedio histórico * 0.15) + (Tendencia semanal * 0.10) + (Favorabilidad * 0.10) + (Redes sociales * 0.05)
     */
    public function calculateFinalProbability(Candidato $candidate): float
    {
        // En una implementación real, estos valores vendrían de la BD agregados.
        // Simularemos las métricas actuales del candidato basados en su BD
        
        $encuestasVerificadas = $this->getPollAverageWithTrust($candidate->id);
        $promedioHistorico = $this->getHistoricalAverage($candidate->id);
        $tendenciaSemanal = $candidate->crecimiento_semanal; // Desde la BD
        $favorabilidad = $candidate->favorabilidad; // Desde la BD
        $redesSociales = $candidate->tendencia_redes; // Desde la BD

        $probabilidadFinal = 
            ($encuestasVerificadas * 0.60) +
            ($promedioHistorico * 0.15) +
            ($tendenciaSemanal * 0.10) +
            ($favorabilidad * 0.10) +
            ($redesSociales * 0.05);

        // Limitar entre 0 y 100
        $probabilidadFinal = max(0, min(100, $probabilidadFinal));

        // Registrar en logs de IA / Predicciones
        Log::info("Predicción calculada para {$candidate->nombre}: {$probabilidadFinal}%");

        return round($probabilidadFinal, 2);
    }

    private function getPollAverageWithTrust(int $candidateId): float
    {
        // 1. Obtener los resultados de encuestas del candidato
        // 2. Multiplicar cada resultado por el Nivel de Confianza (Trust Level) de la fuente (ej. Invamer 95%)
        // 3. Retornar el promedio ponderado. 
        // Valor simulado para el scaffolding:
        return 25.5; 
    }

    private function getHistoricalAverage(int $candidateId): float
    {
        // Promedio de las últimas 5 elecciones o mediciones del último año.
        return 20.0;
    }
}
