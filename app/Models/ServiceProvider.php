<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceProvider extends Model
{
    protected $table = 'service_providers';

    protected $fillable = [
        'provider_name',
        'provider_location',
        'focalpoint_name',
        'focalpoint_email',
        'focalpoint_number',
        'type_services_proposes',
        'created_by',
    ];

    protected $casts = [
        'provider_location' => 'array',
        'type_services_proposes' => 'array',
    ];

    // On stocke JSON dans un champ text
    public function getTypeServicesProposesAttribute($value): array
    {
        if (!$value) return [];
        $decoded = json_decode($value, true);
        return is_array($decoded) ? $decoded : [];
    }

    public function setTypeServicesProposesAttribute($value): void
    {
        if (is_array($value)) {
            $this->attributes['type_services_proposes'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['type_services_proposes'] = $value;
        }
    }

    public function referencements(): HasMany
    {
        return $this->hasMany(Referencement::class, 'provider_id', 'id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
