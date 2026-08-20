<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consultation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'manuel_id',
        'date_ouverture',
        'duree_secondes',
        'derniere_page',
    ];

    protected function casts(): array
    {
        return [
            'date_ouverture' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function manuel(): BelongsTo
    {
        return $this->belongsTo(Manuel::class);
    }
}
