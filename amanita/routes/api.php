<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CircleController;
use App\Http\Controllers\Api\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
// Recuperación de contraseña
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::get('/health', function () {
    return response('ok', 200)
        ->header('Content-Type', 'text/plain');
});

// Rutas protegidas con token de Sanctum
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me',      [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/circles', [CircleController::class, 'index']);
    Route::post('/circles', [CircleController::class, 'store']);

    // Rutas de Tareas
    Route::get('/circles/{circle}/tasks', [TaskController::class, 'index']);  // Ver tareas de un círculo
    Route::post('/circles/{circle}/tasks', [TaskController::class, 'store']); // Crear tarea en un círculo
    Route::patch('/tasks/{task}', [TaskController::class, 'update']);         // Actualizar tarea (status)
});