<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mouvement extends Model
{
    protected $fillable = [
        'date_mouvement',
        'type_mouvement',
        'source_info',
        'code_province_prov',
        'code_territoire_prov',
        'code_zonesante_prov',
        'localite_prov',
        'code_province_accl',
        'code_territoire_accl',
        'code_zonesante_accl',
        'localite_accl',
        'type_logement',
        'created_by',
        'estim_nbre_menages',
        'estim_nbre_personnes',
        'remarques_mouvement',
        'incident_id',
    ];

    protected $casts = [
        'date_mouvement' => 'date',
        'estim_nbre_menages' => 'integer',
        'estim_nbre_personnes' => 'integer',
    ];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function territoireProv(): BelongsTo
    {
        return $this->belongsTo(Territoire::class, 'code_territoire_prov', 'code_territoire');
    }

    public function territoireAccl(): BelongsTo
    {
        return $this->belongsTo(Territoire::class, 'code_territoire_accl', 'code_territoire');
    }
}
