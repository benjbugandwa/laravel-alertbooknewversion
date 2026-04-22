<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Organisation extends Model
{
    protected $table = 'organisations';
    protected $fillable = [
        'org_sigle',
        'org_name',
        'org_secteur_activite',
        'org_categorie',
    ];

    protected $casts = [
        'org_secteur_activite' => 'array',

    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'org_id');
    }
}
