<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CandidatosController;
use App\Http\Controllers\VotosController;
use App\Http\Controllers\EncuestasController;
use App\Http\Controllers\NoticiasController;
use App\Http\Controllers\PrediccionController;
use App\Http\Controllers\ComentariosController;
use App\Http\Controllers\AdminController;

// --- RUTAS PÚBLICAS ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/google-login', [AuthController::class, 'googleLogin']);
Route::get('/google-config', function() {
    return response()->json(['client_id' => env('GOOGLE_CLIENT_ID')]);
});

Route::get('/candidatos', [CandidatosController::class, 'getAll']);
Route::get('/candidatos/{id}', [CandidatosController::class, 'getById']);

Route::get('/encuestas', [EncuestasController::class, 'getAll']);
Route::get('/encuestas/{id}', [EncuestasController::class, 'getById']);

Route::get('/noticias', [NoticiasController::class, 'getAll']);
Route::get('/noticias/destacadas', [NoticiasController::class, 'getDestacadas']);

Route::get('/prediccion', [PrediccionController::class, 'getPredicciones']);
Route::get('/prediccion/historial', [PrediccionController::class, 'getHistorial']);

Route::get('/comentarios', [ComentariosController::class, 'getAll']);
Route::get('/resultados', [VotosController::class, 'getResultados']);


// --- RUTAS PROTEGIDAS (USUARIOS AUTENTICADOS) ---
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'getProfile']);
    
    Route::post('/votar', [VotosController::class, 'votar']);
    Route::get('/mi-voto', [VotosController::class, 'verificarVoto']);
    
    Route::post('/comentarios', [ComentariosController::class, 'create']);
    Route::delete('/comentarios/{id}', [ComentariosController::class, 'delete']);
    
    // --- RUTAS DE ADMINISTRADOR (Validación de Rol dentro del Controlador) ---
    Route::put('/candidatos/{id}', [CandidatosController::class, 'update']);
    
    Route::post('/encuestas', [EncuestasController::class, 'create']);
    Route::delete('/encuestas/{id}', [EncuestasController::class, 'delete']);
    
    Route::post('/noticias', [NoticiasController::class, 'create']);
    Route::put('/noticias/{id}', [NoticiasController::class, 'update']);
    Route::delete('/noticias/{id}', [NoticiasController::class, 'delete']);
    
    Route::get('/admin/usuarios', [AdminController::class, 'getUsuarios']);
    Route::get('/admin/stats', [AdminController::class, 'getStats']);
    Route::get('/admin/votos', [AdminController::class, 'getVotos']);
    Route::delete('/admin/votos/{id}', [AdminController::class, 'deleteVoto']);
    Route::put('/admin/usuarios/{id}/toggle', [AdminController::class, 'toggleUsuario']);
});
