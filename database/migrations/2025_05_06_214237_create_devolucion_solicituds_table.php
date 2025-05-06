<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('devolucion_solicituds', function (Blueprint $table) {
            $table->id();
            
            // Relación con el préstamo
            $table->foreignId('prestamo_id')
                  ->constrained('prestamos')
                  ->onDelete('cascade');
                  
            // Relación con el usuario que solicita
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Estado de la solicitud
            $table->enum('estado', [
                'pendiente',
                'aprobada', 
                'rechazada',
                'completada'
            ])->default('pendiente');
            
            // Motivo opcional para rechazo
            $table->text('motivo_rechazo')->nullable();
            
            // Fechas importantes
            $table->dateTime('fecha_solicitud');
            $table->dateTime('fecha_resolucion')->nullable();
            
            // Admin que procesó la solicitud
            $table->foreignId('admin_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null');
            
            $table->timestamps();
            
            // Índices para mejorar el rendimiento
            $table->index('estado');
            $table->index('fecha_solicitud');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devolucion_solicituds');
    }
};
