<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // PostgreSQL : convertir provider_location (string) -> jsonb
        DB::statement("
            ALTER TABLE service_providers
            ALTER COLUMN provider_location TYPE jsonb
            USING (
                CASE
                    WHEN provider_location IS NULL OR provider_location = '' THEN '[]'::jsonb
                    ELSE provider_location::jsonb
                END
            )
        ");

        // Pareil pour type_services_proposes : text -> jsonb
        DB::statement("
            ALTER TABLE service_providers
            ALTER COLUMN type_services_proposes TYPE jsonb
            USING (
                CASE
                    WHEN type_services_proposes IS NULL OR type_services_proposes = '' THEN '[]'::jsonb
                    ELSE type_services_proposes::jsonb
                END
            )
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE service_providers ALTER COLUMN provider_location TYPE varchar(255)");
        DB::statement("ALTER TABLE service_providers ALTER COLUMN type_services_proposes TYPE text");
    }
};
