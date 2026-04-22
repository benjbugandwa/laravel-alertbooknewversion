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
        Schema::table('incidents', function (Blueprint $table) {
            // survivant_id -> survivants.id (uuid)
            $table->uuid('survivant_id')->nullable()->after('id');

            $table->foreign('survivant_id')
                ->references('id')->on('survivants');
        });

        // Index utiles pour filtres
        Schema::table('incidents', function (Blueprint $table) {
            /*  $table->index('date_incident');
            $table->index('statut_incident');
            $table->index('severite');*/
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['survivant_id']);
            $table->dropColumn('survivant_id');


            $table->dropIndex(['severite']);

            $table->dropIndex(['code_territoire']);
            $table->dropIndex(['code_zonesante']);
        });
    }
};
