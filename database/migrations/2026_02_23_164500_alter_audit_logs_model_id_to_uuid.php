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
        // On passe model_id de integer -> uuid (en texte UUID)
        // Comme ton audit_logs.model_id est vide ou contient des ints, on convertit en text puis uuid si possible.
        // Si tu as déjà des données non convertibles, on les mettra à NULL.

        /*   DB::statement('ALTER TABLE audit_logs ALTER COLUMN model_id DROP DEFAULT');

        // 1) changer le type en uuid avec conversion sûre
        DB::statement("
            ALTER TABLE audit_logs
            ALTER COLUMN model_id TYPE uuid
            USING (
                CASE
                    WHEN model_id IS NULL THEN NULL
                    ELSE NULL
                END
            )
        ");*/
        // Note: on force NULL pour les anciennes valeurs integer (pas convertibles en uuid).
        // Si tu veux conserver l'ancienne valeur, on la sauvegarde dans action_meta avant (optionnel).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Retour arrière: uuid -> integer (perte possible)
        /* DB::statement("
            ALTER TABLE audit_logs
            ALTER COLUMN model_id TYPE integer
            USING NULL
        ");*/
    }
};
