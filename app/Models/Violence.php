<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Violence extends Model
{
    protected $table = 'violences';
    public $timestamps = false;

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'violence_name',
        'categorie_name',
        'violence_description',
    ];

    public function incidents(): BelongsToMany
    {
        return $this->belongsToMany(Incident::class, 'violence_incidents', 'id_violence', 'id_incident')
            ->withPivot(['id', 'description_violence', 'created_by', 'created_at']);
    }
}
