<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Niveau extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'ordre'];

    public function eleves(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function enseignants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_niveau');
    }

    public function manuels(): BelongsToMany
    {
        return $this->belongsToMany(Manuel::class, 'manuel_niveau');
    }
}
