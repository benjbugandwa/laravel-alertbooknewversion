<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zonesante extends Model
{
    protected $table = 'zonesantes';
    protected $primaryKey = 'code_zonesante';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_zonesante', 'nom_zonesante', 'code_territoire'];

    public function territoire(): BelongsTo
    {
        return $this->belongsTo(Territoire::class, 'code_territoire', 'code_territoire');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'code_zonesante', 'code_zonesante');
    }
}
