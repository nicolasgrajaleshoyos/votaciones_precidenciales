<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Voto;
use App\Models\Encuesta;
use App\Models\Noticia;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    // Verificación auxiliar de administrador
    private function isAdmin(Request $request)
    {
        return $request->user() && $request->user()->rol === 'admin';
    }

    // Obtener todos los usuarios
    public function getUsuarios(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $usuarios = User::leftJoin('votos', 'usuarios.id', '=', 'votos.usuario_id')
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.email', 'usuarios.rol', 'usuarios.cedula', 'usuarios.departamento', 'usuarios.ciudad', 'usuarios.fecha_registro', 'usuarios.ultimo_login', 'usuarios.activo')
            ->selectRaw('COUNT(votos.id) as ha_votado')
            ->groupBy('usuarios.id', 'usuarios.nombre', 'usuarios.email', 'usuarios.rol', 'usuarios.cedula', 'usuarios.departamento', 'usuarios.ciudad', 'usuarios.fecha_registro', 'usuarios.ultimo_login', 'usuarios.activo')
            ->orderBy('usuarios.fecha_registro', 'desc')
            ->get();

        return response()->json($usuarios);
    }

    // Obtener estadísticas generales del panel
    public function getStats(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $totalUsuarios = User::count();
        $totalVotos = Voto::count();
        $totalEncuestas = Encuesta::count();
        $totalNoticias = Noticia::count();
        $totalComentarios = Comentario::count();

        // Participación
        $participacion = $totalUsuarios > 0 ? round(($totalVotos / $totalUsuarios) * 100, 2) : 0;

        // Votos por departamento
        $votosDept = Voto::join('usuarios', 'votos.usuario_id', '=', 'usuarios.id')
            ->select('usuarios.departamento')
            ->selectRaw('COUNT(votos.id) as votos')
            ->whereNotNull('usuarios.departamento')
            ->groupBy('usuarios.departamento')
            ->orderBy('votos', 'desc')
            ->get();

        // Registros en los últimos 7 días
        $registrosRecientes = User::selectRaw("DATE(fecha_registro) as fecha, COUNT(*) as registros")
            ->where('fecha_registro', '>=', now()->subDays(7))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        // Votos de hoy agrupados por hora
        $votosHoy = Voto::selectRaw("strftime('%H', fecha_voto) as hora, COUNT(*) as votos")
            ->whereDate('fecha_voto', today())
            ->groupBy('hora')
            ->orderBy('hora', 'asc')
            ->get();

        return response()->json([
            'total_usuarios' => $totalUsuarios,
            'total_votos' => $totalVotos,
            'total_encuestas' => $totalEncuestas,
            'total_noticias' => $totalNoticias,
            'total_comentarios' => $totalComentarios,
            'participacion' => $participacion,
            'votos_por_departamento' => $votosDept,
            'registros_recientes' => $registrosRecientes,
            'votos_hoy' => $votosHoy
        ]);
    }

    // Obtener todos los votos
    public function getVotos(Request $request)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $votos = Voto::join('usuarios', 'votos.usuario_id', '=', 'usuarios.id')
            ->join('candidatos', 'votos.candidato_id', '=', 'candidatos.id')
            ->select('votos.id', 'votos.fecha_voto', 'votos.ip_address', 'usuarios.nombre as usuario_nombre', 'usuarios.email as usuario_email', 'usuarios.departamento', 'candidatos.nombre as candidato_nombre', 'candidatos.partido')
            ->orderBy('votos.fecha_voto', 'desc')
            ->get();

        return response()->json($votos);
    }

    // Eliminar voto
    public function deleteVoto(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $voto = Voto::find($id);

        if (!$voto) {
            return response()->json(['error' => 'Voto no encontrado.'], 404);
        }

        $voto->delete();

        return response()->json(['message' => 'Voto eliminado exitosamente.']);
    }

    // Activar/desactivar usuario
    public function toggleUsuario(Request $request, $id)
    {
        if (!$this->isAdmin($request)) {
            return response()->json(['error' => 'Acceso denegado.'], 403);
        }

        $usuario = User::find($id);

        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado.'], 404);
        }

        if ($usuario->rol === 'admin') {
            return response()->json(['error' => 'No se puede modificar a un administrador.'], 400);
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return response()->json(['message' => 'Estado de usuario actualizado.']);
    }
}
