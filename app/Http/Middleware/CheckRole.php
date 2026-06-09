<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: CheckRole
 *
 * Verifica se o usuário possui um dos perfis necessários.
 * Uso: Route::middleware('role:Administrador')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->is_active) {
            return redirect()->route('login')
                ->with('error', 'Acesso negado. Usuário inativo ou não autenticado.');
        }

        if (! empty($roles) && ! in_array($user->role->name, $roles)) {
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
}
