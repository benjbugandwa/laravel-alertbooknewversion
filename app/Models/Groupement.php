<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Groupement extends Model
{
    protected $table = 'groupements';
    protected $primaryKey = 'code_groupement';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_groupement', 'nom_groupement', 'code_chefferie'];

    public function chefferie(): BelongsTo
    {
        return $this->belongsTo(Chefferie::class, 'code_chefferie', 'code_chefferie');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'code_groupement', 'code_groupement');
    }
}
