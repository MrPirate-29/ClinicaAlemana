<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $nomRol = DB::table('rol')
            ->where('id_rol', Auth::user()->id_rol)
            ->value('nom_rol');

        if ($nomRol !== $role) {
            abort(403, 'No tienes permiso para acceder a este módulo.');
        }

        return $next($request);
    }
}
