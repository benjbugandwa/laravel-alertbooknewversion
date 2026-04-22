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
        Schema::create('survivants', function (Blueprint $table) {
            // ID UUID (conforme au script SQL)
            $table->uuid('id')->primary();

            // Champs prévus dans le script
            $table->string('code_survivant')->unique(); // UNIQUE
            $table->string('full_name');
            $table->integer('age_survivant')->nullable();
            $table->string('sexe_survivant')->nullable();
            $table->string('marital_status')->nullable();
            $table->boolean('disability_status')->nullable();
            $table->text('observations')->nullable();

            // --- Champs additionnels demandés ---
            $table->text('adresses')->nullable();
            $table->boolean('est_mineure')->default(false);
            $table->string('tuteur_nom')->nullable();
            $table->string('tuteur_numero')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->foreign('created_by')->references('id')->on('users');

            // Pas de created_at / updated_at
            // (le script SQL ne les définit pas)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survivants');
    }
};
