<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DevolucionSolicitud extends Model
{
    use HasFactory;

    protected $fillable = [
        'prestamo_id',
        'user_id',
        'estado',
        'motivo_rechazo',
        'fecha_solicitud',
        'fecha_resolucion',
        'admin_id'
    ];

    protected $casts = [
        'fecha_solicitud' => 'datetime',
        'fecha_resolucion' => 'datetime',
    ];

    // Relaciones
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes para consultas comunes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobadas($query)
    {
        return $query->where('estado', 'aprobada');
    }

    public function scopeRechazadas($query)
    {
        return $query->where('estado', 'rechazada');
    }

    // Métodos para cambiar estados
    public function marcarComoAprobada($adminId)
    {
        $this->update([
            'estado' => 'aprobada',
            'fecha_resolucion' => now(),
            'admin_id' => $adminId
        ]);
    }

    public function marcarComoRechazada($adminId, $motivo = null)
    {
        $this->update([
            'estado' => 'rechazada',
            'motivo_rechazo' => $motivo,
            'fecha_resolucion' => now(),
            'admin_id' => $adminId
        ]);
    }

    public function marcarComoCompletada()
    {
        $this->update([
            'estado' => 'completada',
            'fecha_resolucion' => now()
        ]);
    }

    // Helpers para verificar estado
    public function estaPendiente()
    {
        return $this->estado === 'pendiente';
    }

    public function fueAprobada()
    {
        return $this->estado === 'aprobada';
    }

    public function fueRechazada()
    {
        return $this->estado === 'rechazada';
    }

    public function estaCompletada()
    {
        return $this->estado === 'completada';
    }
}