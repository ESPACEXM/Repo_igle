<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if ($role === 'leader' && !$user->isLeader()) {
            abort(403, 'Acceso denegado. Se requiere rol de líder.');
        }

        if ($role === 'member' && !$user->isMember()) {
            abort(403, 'Acceso denegado. Se requiere rol de miembro.');
        }

        return $next($request);
    }
}
