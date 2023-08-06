<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {

        if (Auth::check() && Auth::user()->state != 'A') {
            return redirect('/')->with('error', 'Tu cuenta está inactiva. Contacta al administrador para obtener más información.');
        }

        return $next($request);
    }
}
