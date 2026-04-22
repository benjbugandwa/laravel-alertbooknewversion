<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    // Pas d'auto increment si tu veux gérer l'id manuellement
    public $incrementing = true;
    protected $keyType = 'int';

    public $timestamps = true;
    // Pas de created_at / updated_at dans ton script

    protected $fillable = [
        'id',
        'user_id',
        'user_action',
        'model_type',
        'model_id',
        'ip_address',
        'action_meta',
    ];

    protected $casts = [
        'action_meta' => 'array', // Permet json automatique
        'model_id' => 'string',
    ];

    // Relation vers User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
