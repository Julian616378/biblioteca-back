<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    /**
     * No redirigir nunca, solo devolver 401 si no está autenticado (ideal para API)
     */
    protected function redirectTo(Request $request): ?string
    {
        return null;
    }
}