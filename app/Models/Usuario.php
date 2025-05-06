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
}