<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): mixed
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user->premiere_connexion) {
                    return redirect()->route('password.change');
                }

                return redirect(match ($user->role) {
                    'direction'   => route('direction.dashboard'),
                    'chef_projet' => route('chef_projet.dashboard'),
                    'pointeur'    => route('pointeur.dashboard'),
                    default       => '/',
                });
            }
        }

        return $next($request);
    }
}
