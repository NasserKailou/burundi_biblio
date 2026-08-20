<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_ENSEIGNANT = 'enseignant';

    public const ROLE_ELEVE = 'eleve';

    protected $fillable = [
        'nom',
        'prenom',
        'identifiant',
        'email',
        'password',
        'role_id',
        'niveau_id',
        'classe',
        'actif',
        'peut_publier_commun',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'actif' => 'boolean',
            'peut_publier_commun' => 'boolean',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function niveau(): BelongsTo
    {
        return $this->belongsTo(Niveau::class);
    }

    /**
     * Niveaux additionnels pour un enseignant rattache a plusieurs niveaux.
     */
    public function niveauxEnseignes(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'user_niveau');
    }

    public function manuelsPublies(): HasMany
    {
        return $this->hasMany(Manuel::class, 'uploader_id');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class);
    }

    public function manuelsFavoris(): BelongsToMany
    {
        return $this->belongsToMany(Manuel::class, 'favoris')->withTimestamps();
    }

    public function logsAudit(): HasMany
    {
        return $this->hasMany(LogAudit::class);
    }

    public function isAdmin(): bool
    {
        return $this->role?->libelle === self::ROLE_ADMIN;
    }

    public function isEnseignant(): bool
    {
        return $this->role?->libelle === self::ROLE_ENSEIGNANT;
    }

    public function isEleve(): bool
    {
        return $this->role?->libelle === self::ROLE_ELEVE;
    }

    /**
     * Tous les niveaux geres par un enseignant (niveau principal + niveaux additionnels).
     *
     * @return array<int>
     */
    public function idsNiveauxGeres(): array
    {
        $ids = $this->niveauxEnseignes()->pluck('niveaux.id')->all();

        if ($this->niveau_id) {
            $ids[] = $this->niveau_id;
        }

        return array_values(array_unique($ids));
    }

    public function nomComplet(): string
    {
        return trim("{$this->prenom} {$this->nom}");
    }
}
