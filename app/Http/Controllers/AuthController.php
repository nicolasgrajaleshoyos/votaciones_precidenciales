<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Voto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Registro
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|trim|max:100',
            'email' => 'required|string|email|max:150|unique:usuarios,email',
            'password' => 'required|string|min:6',
            'cedula' => 'nullable|string|max:20|unique:usuarios,cedula',
            'departamento' => 'nullable|string|max:100',
            'ciudad' => 'nullable|string|max:100',
        ], [
            'email.unique' => 'El email ya está registrado.',
            'cedula.unique' => 'La cédula ya está registrada.',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = User::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'cedula' => $request->cedula,
            'departamento' => $request->departamento,
            'ciudad' => $request->ciudad,
            'rol' => 'usuario',
            'activo' => true,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Usuario registrado exitosamente',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
            ]
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $user = User::where('email', $request->email)->where('activo', 1)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash)) {
            return response()->json(['error' => 'Credenciales inválidas.'], 401);
        }

        // Actualizar último login
        $user->ultimo_login = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;

        // Verificar si ya votó
        $voto = Voto::where('usuario_id', $user->id)->first();

        return response()->json([
            'message' => 'Login exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
                'departamento' => $user->departamento,
                'ciudad' => $user->ciudad,
                'ya_voto' => $voto !== null,
                'candidato_votado' => $voto ? $voto->candidato_id : null,
            ]
        ]);
    }

    // Login con Google
    public function googleLogin(Request $request)
    {
        $email = $request->email;
        $nombre = $request->nombre;

        if ($request->has('token')) {
            $parts = explode('.', $request->token);
            if (count($parts) === 3) {
                $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $parts[1])), true);
                if ($payload && isset($payload['email'])) {
                    $email = $payload['email'];
                    $nombre = $payload['name'] ?? $email;
                } else {
                    return response()->json(['error' => 'Token de Google inválido.'], 400);
                }
            } else {
                return response()->json(['error' => 'Formato de token incorrecto.'], 400);
            }
        }

        if (!$email || !$nombre) {
            return response()->json(['error' => 'Email y nombre son requeridos.'], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) {
            $user = User::create([
                'nombre' => $nombre,
                'email' => $email,
                'password_hash' => Hash::make(bin2hex(random_bytes(16))),
                'rol' => 'usuario',
                'activo' => true,
            ]);
        }

        if (!$user->activo) {
            return response()->json(['error' => 'Usuario inactivo.'], 403);
        }

        $user->ultimo_login = now();
        $user->save();

        $token = $user->createToken('auth_token')->plainTextToken;
        $voto = Voto::where('usuario_id', $user->id)->first();

        return response()->json([
            'message' => 'Login con Google exitoso',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'email' => $user->email,
                'rol' => $user->rol,
                'departamento' => $user->departamento,
                'ciudad' => $user->ciudad,
                'ya_voto' => $voto !== null,
                'candidato_votado' => $voto ? $voto->candidato_id : null,
            ]
        ]);
    }

    // Obtener Perfil
    public function getProfile(Request $request)
    {
        $user = $request->user();

        $voto = Voto::with('candidato')
            ->where('usuario_id', $user->id)
            ->first();

        return response()->json([
            'id' => $user->id,
            'nombre' => $user->nombre,
            'email' => $user->email,
            'rol' => $user->rol,
            'cedula' => $user->cedula,
            'departamento' => $user->departamento,
            'ciudad' => $user->ciudad,
            'fecha_registro' => $user->fecha_registro,
            'ya_voto' => $voto !== null,
            'voto' => $voto ? [
                'candidato_id' => $voto->candidato_id,
                'candidato_nombre' => $voto->candidato->nombre,
            ] : null,
        ]);
    }
}
