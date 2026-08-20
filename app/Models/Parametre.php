<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    protected $table = 'parametres';

    protected $primaryKey = 'cle';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['cle', 'valeur'];

    public static function get(string $cle, ?string $defaut = null): ?string
    {
        return Cache::remember("parametre:{$cle}", 300, function () use ($cle, $defaut) {
            return static::query()->find($cle)?->valeur ?? $defaut;
        });
    }

    public static function set(string $cle, ?string $valeur): void
    {
        static::query()->updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
        Cache::forget("parametre:{$cle}");
    }
}
