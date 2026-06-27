<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckPremiereConnexion
{
    public function handle(Request $request, Closure $next): mixed
    {
        if (auth()->check() && auth()->user()->premiere_connexion) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
