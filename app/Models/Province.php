<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Province extends Model
{
    protected $table = 'provinces';
    protected $primaryKey = 'code_province';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_province', 'nom_province', 'is_active'];

    protected static function booted(): void
    {
        static::addGlobalScope('active', function (\Illuminate\Database\Eloquent\Builder $builder) {
            $builder->where('provinces.is_active', 'YES');
        });
    }

    public function territoires(): HasMany
    {
        return $this->hasMany(Territoire::class, 'code_province', 'code_province');
    }

    public function zonesSantes(): HasManyThrough
    {
        return $this->hasManyThrough(
            ZoneSante::class,
            Territoire::class,
            'code_province',    // FK on territoires
            'code_territoire',  // FK on zonesantes
            'code_province',
            'code_territoire'
        );
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'code_province', 'code_province');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'code_province', 'code_province');
    }
}
