<?php

namespace App\Services;

use App\Models\LogAudit;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditService
{
    /**
     * Enregistre une action sensible (connexion, upload, suppression,
     * changement de config, etc.) dans logs_audit. Ne doit jamais faire
     * echouer l'action principale : les erreurs de journalisation sont
     * volontairement avalees en dernier recours (best-effort).
     */
    public function log(string $action, ?string $cible = null, ?User $user = null): void
    {
        try {
            LogAudit::query()->create([
                'user_id' => $user?->id ?? Auth::id(),
                'action' => $action,
                'cible' => $cible,
                'ip' => Request::ip(),
            ]);
        } catch (\Throwable) {
            // Best-effort : l'audit ne doit jamais bloquer le flux utilisateur.
        }
    }
}
