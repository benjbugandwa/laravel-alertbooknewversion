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
        Schema::create('airesantes', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();

            $table->string('code_airesante', 20)->primary();
            $table->string('nom_airesante')->unique();
            $table->string('code_zonesante', 20)->nullable();

            $table->foreign('code_zonesante')
                ->references('code_zonesante')
                ->on('zonesantes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('airesantes');
    }
};
