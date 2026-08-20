<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Restreint une route a un ou plusieurs roles. Usage : ->middleware('role:admin,enseignant')
 * Le filtrage des DONNEES (niveau, proprietaire...) reste toujours fait au niveau des
 * requetes Eloquent (scopes) - ce middleware ne fait que la porte d'entree sur les routes.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->role || ! in_array($user->role->libelle, $roles, true)) {
            abort(403, "Vous n'avez pas les droits necessaires pour acceder a cette ressource.");
        }

        return $next($request);
    }
}
