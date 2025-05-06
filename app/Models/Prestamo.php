<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
/**
 * @property int $multa
 */

class Prestamo extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'libro_id',
        'fecha_prestamo',
        'fecha_devolucion',
        'devuelto',
        'multa', 
    ];

    public function user()
{
    return $this->belongsTo(User::class);
}

public function libro()
{
    return $this->belongsTo(Libro::class);
}
// Agregar esta relación al modelo Prestamo
public function solicitudesDevolucion()
{
    return $this->hasMany(DevolucionSolicitud::class);
}

public function solicitudDevolucionPendiente()
{
    return $this->hasOne(DevolucionSolicitud::class)
                ->where('estado', 'pendiente');
}
}

