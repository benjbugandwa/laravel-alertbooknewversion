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
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->timestamp('updated_at')->nullable();
        });

        // Remplir created_at pour les anciennes lignes (si la table contient déjà des données)
        DB::table('audit_logs')->whereNull('created_at')->update([
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Puis on remet created_at non-null + default si tu veux
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->timestamp('created_at')->nullable(false)->default(DB::raw('CURRENT_TIMESTAMP'))->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            Schema::table('audit_logs', function (Blueprint $table) {
                $table->dropColumn(['created_at', 'updated_at']);
            });
        });
    }
};
