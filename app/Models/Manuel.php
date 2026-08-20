<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Manuel extends Model
{
    public const STATUT_PUBLIE = 'publie';

    public const STATUT_BROUILLON = 'brouillon';

    public const TYPE_PDF = 'pdf';

    public const TYPE_EPUB = 'epub';

    protected $fillable = [
        'titre',
        'description',
        'auteur',
        'annee',
        'matiere_id',
        'fichier',
        'couverture',
        'type',
        'est_commun',
        'uploader_id',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'est_commun' => 'boolean',
        ];
    }

    public function matiere(): BelongsTo
    {
        return $this->belongsTo(Matiere::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function niveaux(): BelongsToMany
    {
        return $this->belongsToMany(Niveau::class, 'manuel_niveau');
    }

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }

    public function favoris(): HasMany
    {
        return $this->hasMany(Favori::class);
    }

    public function scopePublies(Builder $query): Builder
    {
        return $query->where('statut', self::STATUT_PUBLIE);
    }

    /**
     * Filtre le catalogue selon le role : un manuel est visible si il est
     * "commun" OU s'il cible le niveau de l'utilisateur. Toujours applique
     * cote requete (jamais uniquement cote vue) - cf. section 5 du cahier
     * des charges.
     */
    public function scopeVisiblePour(Builder $query, User $user): Builder
    {
        if ($user->isAdmin()) {
            return $query;
        }

        if ($user->isEnseignant()) {
            $niveauxGeres = $user->idsNiveauxGeres();

            return $query->where(function (Builder $q) use ($user, $niveauxGeres) {
                $q->where('uploader_id', $user->id)
                    ->orWhereHas('niveaux', fn (Builder $n) => $n->whereIn('niveaux.id', $niveauxGeres));
            });
        }

        // Eleve : son niveau + les manuels "communs", uniquement publies.
        return $query->publies()->where(function (Builder $q) use ($user) {
            $q->where('est_commun', true)
                ->orWhereHas('niveaux', fn (Builder $n) => $n->where('niveaux.id', $user->niveau_id));
        });
    }
}
