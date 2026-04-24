<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Chefferie extends Model
{
    protected $table = 'chefferies';
    protected $primaryKey = 'code_chefferie';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_chefferie', 'nom_chefferie', 'code_territoire'];

    public function territoire(): BelongsTo
    {
        return $this->belongsTo(Territoire::class, 'code_territoire', 'code_territoire');
    }

    public function groupements(): HasMany
    {
        return $this->hasMany(Groupement::class, 'code_chefferie', 'code_chefferie');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'code_chefferie', 'code_chefferie');
    }
}
