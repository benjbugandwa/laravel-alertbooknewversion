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
            $table->string('code_chefferie')->nullable();
            $table->foreign('code_chefferie')->references('code_chefferie')->on('chefferies');

            $table->string('code_groupement')->nullable();
            $table->foreign('code_groupement')->references('code_groupement')->on('groupements');

            $table->string('code_airesante')->nullable();
            $table->foreign('code_airesante')->references('code_airesante')->on('airesantes');

            $table->string('code_evenement')->nullable()->default('NA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('code_chefferie');
            $table->dropColumn('code_groupement');
            $table->dropColumn('code_airesante');
            $table->dropColumn('code_evenement');
        });
    }
};
