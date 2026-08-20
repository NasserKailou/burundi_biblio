<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Matiere extends Model
{
    protected $fillable = ['libelle'];

    public function manuels(): HasMany
    {
        return $this->hasMany(Manuel::class);
    }
}
