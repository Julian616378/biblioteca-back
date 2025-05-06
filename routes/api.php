<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LibroController;
use App\Http\Controllers\PrestamoController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);

    Route::get('/libros/{id}', [LibroController::class, 'show']);
    Route::post('/libros', [LibroController::class, 'store']); //admin

    Route::post('/prestamos', [PrestamoController::class, 'store']);
    Route::put('/prestamos/{id}/renovar', [PrestamoController::class, 'renovar']);
    Route::put('/prestamos/{id}/devolver', [PrestamoController::class, 'devolver']);
    Route::get('/prestamos/{id}', [PrestamoController::class, 'show']); 
});
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/libros', [LibroController::class, 'index']);
    Route::post('/prestar', [PrestamoController::class, 'store']);
    Route::get('/prestamos', [PrestamoController::class, 'index']);
    Route::post('/prestamos/{id}/aprobar', [PrestamoController::class, 'aprobar']);
Route::post('/prestamos/{id}/renovar', [PrestamoController::class, 'renovar']);
Route::get('/libros-solicitados', [PrestamoController::class, 'librosSolicitados']);
Route::get('/resumen-prestamos', [PrestamoController::class, 'resumen']);

Route::get('/prestamos/pendientes', [PrestamoController::class, 'pendientes']);
// Tus otras rutas API aquí...

});
