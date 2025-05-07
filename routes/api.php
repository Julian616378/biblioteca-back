<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\PrestamoController;

// Rutas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Libros
    Route::get('/libros', [LibroController::class, 'index']);
    Route::get('/libros/{id}', [LibroController::class, 'show']);
    Route::post('/libros', [LibroController::class, 'store']); // Solo admin
    
    // Préstamos
    Route::get('/prestamos', [PrestamoController::class, 'index']);
    Route::post('/prestamos', [PrestamoController::class, 'store']);
    Route::get('/prestamos/{id}', [PrestamoController::class, 'show']);
    Route::put('/prestamos/{id}/devolver', [PrestamoController::class, 'devolver']);
    Route::post('/prestamos/{id}/aprobar', [PrestamoController::class, 'aprobar']);
    
    // Otras rutas de préstamos
    Route::get('/libros-solicitados', [PrestamoController::class, 'librosSolicitados']);
    Route::get('/resumen-prestamos', [PrestamoController::class, 'resumen']);
    Route::get('/prestamos/pendientes', [PrestamoController::class, 'pendientes']);
    Route::put('/prestamos/{id}/fecha-devolucion', [PrestamoController::class, 'actualizarFechaDevolucion']);
    
    // Devoluciones
    Route::post('/devoluciones/{id}/aprobar', [PrestamoController::class, 'aprobarDevolucion']);
    Route::post('/devoluciones/{id}/rechazar', [PrestamoController::class, 'rechazarDevolucion']);
    Route::put('/prestamos/{prestamo}/renovar', [PrestamoController::class, 'renovar']);
});