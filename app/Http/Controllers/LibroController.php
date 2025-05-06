<?php

namespace App\Http\Controllers;

use App\Models\Libro;
use Illuminate\Http\Request;

class LibroController extends Controller
{
    public function index() {
        return Libro::all();
    }

    public function show($id) {
        return Libro::findOrFail($id);
    }

    // Solo para cargar libros (admin)
    public function store(Request $request) {
        $request->validate([
            'titulo' => 'required',
            'autor' => 'required',
            'materia' => 'required',
            'genero' => 'required',
        ]);

        $libro = Libro::create($request->all());
        return response()->json(['mensaje' => 'Libro creado', 'libro' => $libro]);
    }
}
