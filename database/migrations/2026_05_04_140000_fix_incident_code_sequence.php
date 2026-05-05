<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // On PostgreSQL, on synchronise la séquence uniquement avec les codes numériques commençant par 'ALT-'
        // Le filtre regex ~ '^[0-9]+$' évite les erreurs de conversion sur des codes malformés ou d'un autre type.
        DB::statement("
            SELECT setval('incident_code_seq', 
                (SELECT COALESCE(MAX(CAST(SUBSTRING(code_incident FROM 5) AS INTEGER)), 0) 
                 FROM incidents 
                 WHERE code_incident ~ '^ALT-[0-9]+$')
            )
        ");
    }

    public function down(): void
    {
        // Rien à faire
    }
};
