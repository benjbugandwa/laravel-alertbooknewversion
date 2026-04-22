<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            // model_id devient nullable (uuid)
            $table->uuid('model_id')->nullable()->change();

            // model_key servira pour les IDs non-UUID (ex: organisations.id = 2)
            $table->string('model_key')->nullable()->after('model_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropColumn('model_key');
            $table->uuid('model_id')->nullable(false)->change(); // si avant c'était NOT NULL
        });
    }
};
