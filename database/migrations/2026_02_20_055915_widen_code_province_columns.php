<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Drop FK constraints (si elles existent)
        DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_code_province_foreign"');
        //  DB::statement('ALTER TABLE "incidents" DROP CONSTRAINT IF EXISTS "incidents_code_province_foreign"');
        //   DB::statement('ALTER TABLE "territoires" DROP CONSTRAINT IF EXISTS "territoires_code_province_foreign"');

        // 2) Élargir la taille des colonnes code_province
        DB::statement('ALTER TABLE "provinces" ALTER COLUMN "code_province" TYPE varchar(20)');
        DB::statement('ALTER TABLE "users" ALTER COLUMN "code_province" TYPE varchar(20)');
        //  DB::statement('ALTER TABLE "incidents" ALTER COLUMN "code_province" TYPE varchar(20)');
        //  DB::statement('ALTER TABLE "territoires" ALTER COLUMN "code_province" TYPE varchar(20)');

        // 3) Recréer les FK (ON DELETE SET NULL = nullOnDelete)
        DB::statement('ALTER TABLE "users" ADD CONSTRAINT "users_code_province_foreign"
            FOREIGN KEY ("code_province") REFERENCES "provinces"("code_province") ON DELETE SET NULL');

        /*  DB::statement('ALTER TABLE "incidents" ADD CONSTRAINT "incidents_code_province_foreign"
            FOREIGN KEY ("code_province") REFERENCES "provinces"("code_province") ON DELETE SET NULL');

        DB::statement('ALTER TABLE "territoires" ADD CONSTRAINT "territoires_code_province_foreign"
            FOREIGN KEY ("code_province") REFERENCES "provinces"("code_province") ON DELETE SET NULL');*/
    }

    public function down(): void
    {
        // Optionnel : revenir en varchar(3) (pas conseillé)
        DB::statement('ALTER TABLE "users" DROP CONSTRAINT IF EXISTS "users_code_province_foreign"');
        // DB::statement('ALTER TABLE "incidents" DROP CONSTRAINT IF EXISTS "incidents_code_province_foreign"');
        // DB::statement('ALTER TABLE "territoires" DROP CONSTRAINT IF EXISTS "territoires_code_province_foreign"');

        DB::statement('ALTER TABLE "users" ALTER COLUMN "code_province" TYPE varchar(3)');
        //  DB::statement('ALTER TABLE "incidents" ALTER COLUMN "code_province" TYPE varchar(3)');
        //  DB::statement('ALTER TABLE "territoires" ALTER COLUMN "code_province" TYPE varchar(3)');
        DB::statement('ALTER TABLE "provinces" ALTER COLUMN "code_province" TYPE varchar(3)');
    }
};
