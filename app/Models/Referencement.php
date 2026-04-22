<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Referencement extends Model
{
    protected $table = 'referencements';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'code_referencement',
        'id_incident',
        'date_referencement',
        'provider_id',
        'resultat',
        'type_reponse',
        'statut_reponse',
        'besoin_suivi',
        'observations',
        'file_path',
        'created_by',
    ];

    protected $casts = [
        'date_referencement' => 'datetime',
        'besoin_suivi' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ref) {
            if (!$ref->id) $ref->id = (string) Str::uuid();
            if (!$ref->code_referencement) $ref->code_referencement = self::nextCode();
        });
    }

    // Génération robuste REF-000000 (uniquement si format REF-######)
    /*  public static function nextCode(): string
    {
        // advisory lock pour éviter doublons en concurrence
        DB::select("SELECT pg_advisory_lock(987654321)");

        try {
            $row = DB::selectOne("
                SELECT MAX( (regexp_matches(code_referencement, '^REF-(\\d{6})$'))[1]::int ) AS max_num
                FROM referencements
                WHERE code_referencement ~ '^REF-\\d{6}$'
            ");

            $next = ((int)($row->max_num ?? 0)) + 1;
            return 'REF-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
        } finally {
            DB::select("SELECT pg_advisory_unlock(987654321)");
        }
    }*/

    public static function nextCode(): string
    {
        // On utilise xact_lock : pas besoin de unlock manuel, 
        // il se libère dès que le DB::transaction ou le parent finit.
        DB::select("SELECT pg_advisory_xact_lock(987654321)");

        // Utilisation de SUBSTRING avec Regex pour être plus robuste que regexp_matches
        $row = DB::selectOne("
        SELECT MAX(SUBSTRING(code_referencement FROM '[0-9]+')::int) AS max_num
        FROM referencements
        WHERE code_referencement ~ '^REF-[0-9]{6}$'
    ");

        $next = ((int)($row->max_num ?? 0)) + 1;

        return 'REF-' . str_pad((string)$next, 6, '0', STR_PAD_LEFT);
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class, 'id_incident', 'id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id', 'id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id');
    }
}
