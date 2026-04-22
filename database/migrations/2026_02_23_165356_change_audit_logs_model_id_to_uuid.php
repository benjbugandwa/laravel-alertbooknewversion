<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Ajouter une nouvelle colonne uuid
        DB::statement('ALTER TABLE audit_logs ADD COLUMN model_id_uuid uuid NULL');

        // 2) Si tu avais déjà model_id en integer, on ne peut pas convertir.
        // On laisse NULL. (Optionnel: tu peux sauvegarder l'ancien model_id dans action_meta)
        // Exemple: action_meta = action_meta || ' | old_model_id=...' (si tu veux)

        // 3) Supprimer l'ancienne colonne
        DB::statement('ALTER TABLE audit_logs DROP COLUMN model_id');

        // 4) Renommer la nouvelle en model_id
        DB::statement('ALTER TABLE audit_logs RENAME COLUMN model_id_uuid TO model_id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retour arrière : recréer integer (perte de données possible)
        DB::statement('ALTER TABLE audit_logs ADD COLUMN model_id_int integer NULL');
        DB::statement('ALTER TABLE audit_logs DROP COLUMN model_id');
        DB::statement('ALTER TABLE audit_logs RENAME COLUMN model_id_int TO model_id');
    }
};
