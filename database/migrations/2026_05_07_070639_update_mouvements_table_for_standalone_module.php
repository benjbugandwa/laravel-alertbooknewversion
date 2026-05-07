<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            $table->uuid('incident_id')->nullable()->change();
            $table->string('cause_deplacement')->nullable()->after('remarques_mouvement');
        });
    }

    public function down(): void
    {
        Schema::table('mouvements', function (Blueprint $table) {
            $table->uuid('incident_id')->nullable(false)->change();
            $table->dropColumn('cause_deplacement');
        });
    }
};
