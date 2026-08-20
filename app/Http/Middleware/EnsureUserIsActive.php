<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Un compte desactive par l'admin (ou en attente de validation) perd
 * immediatement l'acces, meme si sa session est encore active.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->actif) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['identifiant' => 'Votre compte est desactive ou en attente de validation par un administrateur.']);
        }

        return $next($request);
    }
}
