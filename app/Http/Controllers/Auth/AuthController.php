<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Niveau;
use App\Models\Parametre;
use App\Models\Role;
use App\Models\User;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function __construct(private readonly AuditService $audit)
    {
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'identifiant' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        $throttleKey = Str::lower($credentials['identifiant']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $secondes = RateLimiter::availableIn($throttleKey);

            throw ValidationException::withMessages([
                'identifiant' => "Trop de tentatives. Reessayez dans {$secondes} secondes.",
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey, 60);

            $this->audit->log('connexion_echouee', $credentials['identifiant']);

            throw ValidationException::withMessages([
                'identifiant' => 'Identifiant ou mot de passe incorrect.',
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        $user = $request->user();

        if (! $user->actif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'identifiant' => 'Votre compte est desactive ou en attente de validation par un administrateur.',
            ]);
        }

        $this->audit->log('connexion', (string) $user->id, $user);

        return redirect()->intended($this->redirectionParDefaut($user));
    }

    public function logout(Request $request): RedirectResponse
    {
        $this->audit->log('deconnexion', (string) $request->user()?->id);

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister(): View
    {
        return view('auth.register', [
            'niveaux' => Niveau::query()->orderBy('ordre')->get(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $longueurMin = (int) (Parametre::get('politique_mdp_longueur_min') ?? 8);

        $data = $request->validate([
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'identifiant' => ['required', 'string', 'max:50', 'alpha_dash', 'unique:users,identifiant'],
            'password' => ['required', 'confirmed', Password::min($longueurMin)->mixedCase()->numbers()],
            'niveau_id' => ['required', 'exists:niveaux,id'],
            'classe' => ['nullable', 'string', 'max:50'],
        ]);

        $roleEleve = Role::query()->where('libelle', 'eleve')->firstOrFail();
        $validationAuto = Parametre::get('validation_auto') === 'true';

        $user = User::query()->create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'identifiant' => $data['identifiant'],
            'password' => Hash::make($data['password']),
            'role_id' => $roleEleve->id,
            'niveau_id' => $data['niveau_id'],
            'classe' => $data['classe'] ?? null,
            'actif' => $validationAuto,
        ]);

        $this->audit->log('inscription', (string) $user->id, $user);

        if (! $validationAuto) {
            return redirect()->route('login')->with(
                'status',
                'Votre compte a ete cree. Il doit etre valide par un administrateur avant de pouvoir vous connecter.'
            );
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function redirectionParDefaut(User $user): string
    {
        return match (true) {
            $user->isAdmin() => route('admin.dashboard'),
            $user->isEnseignant() => route('teacher.dashboard'),
            default => route('dashboard'),
        };
    }
}
