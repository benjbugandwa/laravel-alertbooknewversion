<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Airesante extends Model
{
    protected $table = 'airesantes';
    protected $primaryKey = 'code_airesante';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_airesante', 'nom_airesante', 'code_zonesante'];

    public function zonesante(): BelongsTo
    {
        return $this->belongsTo(Zonesante::class, 'code_zonesante', 'code_zonesante');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Airesante::class, 'code_airesante', 'code_airesante');
    }
}
