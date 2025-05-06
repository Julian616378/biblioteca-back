<?php

namespace App\Http\Controllers;

use App\Models\Prestamo;
use App\Models\Libro;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrestamoController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $prestamos = $user->role === 'admin'
            ? Prestamo::with('libro')->get()
            : Prestamo::with('libro')->where('user_id', $user->id)->where('aprobado', true)->get();
    
        foreach ($prestamos as $prestamo) {
            $this->aplicarMultaSiCorresponde($prestamo);
        }
    
        return response()->json($prestamos);
    }
    
    protected function aplicarMultaSiCorresponde(Prestamo $prestamo)
    {
        // Solo aplicar multa si:
        // 1. El préstamo está aprobado
        // 2. No ha sido devuelto
        // 3. No tiene multa previa (0)
        // 4. Hay al menos 1 minuto de retraso
        
        if ($prestamo->aprobado && 
            !$prestamo->devuelto && 
            $prestamo->multa == 0 &&
            Carbon::now()->gt($fechaDevolucion = Carbon::parse($prestamo->fecha_devolucion))) {
            
            $minutosRetraso = Carbon::now()->diffInMinutes($fechaDevolucion);
            
            if ($minutosRetraso >= 1) {
                $prestamo->multa = 3000.00; // Valor decimal
                $prestamo->save();
            }
        }
    }



    public function store(Request $request) {
        $request->validate([
            'libro_id' => 'required|exists:libros,id'
        ]);
    
        $libro = Libro::findOrFail($request->libro_id);
        if (!$libro->disponible) {
            return response()->json(['mensaje' => 'Libro no disponible'], 400);
        }
    
        $fechaPrestamo = Carbon::now();
        $fechaDevolucion = $fechaPrestamo->copy()->addDays(3);
    
        $prestamo = Prestamo::create([
            'user_id' => $request->user()->id,
            'libro_id' => $libro->id,
            'fecha_prestamo' => $fechaPrestamo,
            'fecha_devolucion' => $fechaDevolucion,
            'aprobado' => false
        ]);
        return response()->json(['mensaje' => 'Préstamo solicitado, espera aprobación del administrador', 'prestamo' => $prestamo]);
    }
    


    public function aprobar($id, Request $request) {
        if ($request->user()->role != 'admin') {
            return response()->json(['mensaje' => 'No autorizado'], 403);
        }
    
        $prestamo = Prestamo::findOrFail($id);
        $libro = $prestamo->libro;
    
        if (!$libro->disponible) {
            return response()->json(['mensaje' => 'Libro no disponible'], 400);
        }
    
        // Aprobar el préstamo
        $prestamo->aprobado = true;
        $prestamo->save();
    
        // Marcar el libro como no disponible
        $libro->disponible = false;
        $libro->save();
    
        return response()->json(['mensaje' => 'Préstamo aprobado']);
    }
    




    public function renovar($id, Request $request) {
        $prestamo = Prestamo::where('user_id', $request->user()->id)->findOrFail($id);

        if ($prestamo->devuelto) {
            return response()->json(['mensaje' => 'No se puede renovar un préstamo devuelto'], 400);
        }

        $prestamo->fecha_devolucion = Carbon::parse($prestamo->fecha_devolucion)->addDays(3);
        $prestamo->save();

        return response()->json(['mensaje' => 'Préstamo renovado']);
    }



    public function devolver($id, Request $request)
    {
        $prestamo = Prestamo::where('user_id', $request->user()->id)->findOrFail($id);
    
        if ($prestamo->devuelto) {
            return response()->json(['mensaje' => 'Préstamo ya devuelto'], 400);
        }
    
        $prestamo->devuelto = true;
        $prestamo->save();
    
        $libro = $prestamo->libro;
        if ($libro) {
            $libro->disponible = true;
            $libro->save();
        }
    
        return response()->json(['mensaje' => 'Libro devuelto y ahora disponible para otros préstamos']);
    }
    

   
    
    
    public function show($id)
    {
        $prestamo = Prestamo::with('libro')->findOrFail($id);
        $this->aplicarMultaSiCorresponde($prestamo);
        return response()->json($prestamo);
    }

public function librosSolicitados()
{
    $user = auth()->user();

    $librosSolicitados = Prestamo::where('user_id', $user->id)
        ->where('devuelto', false)
        ->pluck('libro_id'); // Solo devuelve el array de IDs de libros solicitados

    return response()->json(['librosSolicitados' => $librosSolicitados]);
}




public function resumen()
    {
        $user = auth()->user();

        $prestamosActivos = Prestamo::where('user_id', $user->id)
            ->where('aprobado', true)
            ->where('devuelto', false)
            ->get();

        $cantidadPrestamos = $prestamosActivos->count();
        $cantidadMultas = $prestamosActivos->where('multa', '>', 0)->count();
        $valorMultas = $prestamosActivos->sum('multa');

        return response()->json([
            'cantidad_prestamos' => $cantidadPrestamos,
            'cantidad_multas' => $cantidadMultas,
            'valor_total_multas' => $valorMultas
        ]);
    }

}