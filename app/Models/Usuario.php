<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $fillable = ['nombre', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function prestamos() {
        return $this->hasMany(Prestamo::class, 'user_id');
    }
    // Agregar esta relación
// Agregar estas relaciones al modelo User
public function solicitudesDevolucion()
{
    return $this->hasMany(DevolucionSolicitud::class, 'user_id');
}

public function devolucionesProcesadas()
{
    return $this->hasMany(DevolucionSolicitud::class, 'admin_id');
}
}