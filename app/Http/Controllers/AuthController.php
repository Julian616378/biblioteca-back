<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|email|unique:usuarios',
            'password' => 'required|min:4',
            'role' => 'in:usuario,admin'
        ]);

        Usuario::create([
            'nombre' => $request->nombre,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => $request->role ?? 'usuario'
        ]);

        return response()->json(['mensaje' => 'Usuario creado']);
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = Usuario::where('email', $request->email)->first();
    
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['mensaje' => 'Credenciales incorrectas'], 401);
        }
    
        $token = $user->createToken('token')->plainTextToken;
    
        // Asegúrate de incluir el rol en la respuesta
        return response()->json([
            'usuario' => $user,
            'token' => $token,
            'role' => $user->role // Asegúrate de enviar el role
        ]);
    }
    

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->json(['mensaje' => 'Sesión cerrada']);
    }

    public function user(Request $request) {
        return $request->user();
    }
}
