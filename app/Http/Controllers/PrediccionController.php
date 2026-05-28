<?php

namespace App\Http\Controllers;

use App\Models\Candidato;
use App\Models\TendenciaRedes;
use App\Models\Voto;
use App\Models\PrediccionHistorico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrediccionController extends Controller
{
    // Obtener predicciones electorales
    public function getPredicciones()
    {
        $totalVotos = Voto::count() ?: 1;

        // 1. Obtener candidatos con intención de voto
        $candidatos = Candidato::where('activo', true)
            ->withCount('votos')
            ->get()
            ->map(function ($c) use ($totalVotos) {
                return [
                    'id' => $c->id,
                    'nombre' => $c->nombre,
                    'partido' => $c->partido,
                    'tendencia' => $c->tendencia,
                    'color_partido' => $c->color_partido,
                    'foto_url' => $c->foto_url,
                    'favorabilidad' => floatval($c->favorabilidad),
                    'tendencia_redes' => floatval($c->tendencia_redes),
                    'crecimiento_semanal' => floatval($c->crecimiento_semanal),
                    'votos' => $c->votos_count,
                    'intencion_voto' => round(($c->votos_count * 100.0) / $totalVotos, 2)
                ];
            });

        // 2. Obtener datos de redes sociales agrupados por candidato
        $redesData = TendenciaRedes::select('candidato_id')
            ->selectRaw('SUM(menciones) as total_menciones')
            ->selectRaw('AVG(sentimiento) as avg_sentimiento')
            ->selectRaw('SUM(interacciones) as total_interacciones')
            ->groupBy('candidato_id')
            ->get();

        $maxMenciones = $redesData->max('total_menciones') ?: 1;
        $redesMap = $redesData->keyBy('candidato_id');

        // 3. Calcular probabilidad
        $predicciones = $candidatos->map(function ($c) use ($redesMap, $maxMenciones) {
            $intencionVoto = $c['intencion_voto'];
            $favorabilidad = $c['favorabilidad'];

            // Normalización de redes
            $redes = $redesMap->get($c['id']);
            $redesScore = $c['tendencia_redes'];
            if ($redes) {
                $mencionesNorm = ($redes->total_menciones / $maxMenciones) * 100;
                $sentimientoNorm = floatval($redes->avg_sentimiento);
                $redesScore = ($mencionesNorm * 0.4) + ($sentimientoNorm * 0.6);
            }

            // Normalizar crecimiento semanal
            $crecimiento = $c['crecimiento_semanal'];
            $crecimientoNorm = max(0, min(100, ($crecimiento + 10) * 5));

            // Fórmula algoritmo
            $probabilidad = ($intencionVoto * 0.5) + ($favorabilidad * 0.2) + ($redesScore * 0.2) + ($crecimientoNorm * 0.1);

            return [
                'id' => $c['id'],
                'nombre' => $c['nombre'],
                'partido' => $c['partido'],
                'tendencia' => $c['tendencia'],
                'color_partido' => $c['color_partido'],
                'foto_url' => $c['foto_url'],
                'votos' => $c['votos'],
                'intencion_voto' => $intencionVoto,
                'favorabilidad' => $favorabilidad,
                'redes_score' => round($redesScore, 2),
                'crecimiento_semanal' => $crecimiento,
                'probabilidad' => round($probabilidad, 2),
                'desglose' => [
                    'comp_intencion' => round($intencionVoto * 0.5, 2),
                    'comp_favorabilidad' => round($favorabilidad * 0.2, 2),
                    'comp_redes' => round($redesScore * 0.2, 2),
                    'comp_crecimiento' => round($crecimientoNorm * 0.1, 2)
                ]
            ];
        })->sortByDesc('probabilidad')->values();

        // Normalizar probabilidades a que sumen 100
        $totalProb = $predicciones->sum('probabilidad') ?: 1;
        $predicciones = $predicciones->map(function ($p) use ($totalProb) {
            $p['probabilidad_normalizada'] = round(($p['probabilidad'] / $totalProb) * 100, 2);
            return $p;
        });

        $masOpcionado = $predicciones->first() ?: null;

        return response()->json([
            'predicciones' => $predicciones,
            'mas_opcionado' => $masOpcionado,
            'total_votos' => $totalVotos,
            'formula' => '(Intención de voto × 0.5) + (Favorabilidad × 0.2) + (Redes sociales × 0.2) + (Crecimiento semanal × 0.1)',
            'ultima_actualizacion' => now()->toIso8601String()
        ]);
    }

    // Obtener historial
    public function getHistorial()
    {
        $historial = PrediccionHistorico::join('candidatos', 'predicciones_historico.candidato_id', '=', 'candidatos.id')
            ->select('predicciones_historico.*', 'candidatos.nombre as candidato_nombre', 'candidatos.color_partido')
            ->orderBy('predicciones_historico.fecha_calculo', 'desc')
            ->limit(100)
            ->get();

        return response()->json($historial);
    }
}
