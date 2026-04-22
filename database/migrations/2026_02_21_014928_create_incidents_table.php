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
        Schema::create('incidents', function (Blueprint $table) {
            // SQL: "id" uuid (pas explicitement PK dans le script, mais on le met PK côté Laravel)
            $table->uuid('id')->primary();

            // SQL: "code_incident" varchar UNIQUE
            $table->string('code_incident')->unique();

            // SQL fields
            $table->timestamp('date_incident')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->string('severite')->nullable();
            $table->string('statut_incident')->nullable();
            $table->string('auteur_presume')->nullable();

            $table->string('code_province', 20)->nullable();
            $table->string('code_territoire', 20)->nullable();
            $table->string('code_zonesante', 20)->nullable();

            $table->string('localite')->nullable();
            $table->string('source_info')->nullable();
            $table->text('description_faits')->nullable();

            // SQL: "created_at" timestamp (pas de updated_at dans le script)
            $table->timestamp('created_at')->nullable();

            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('assigned_by')->nullable();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('last_status_changed_at')->nullable();

            $table->string('confidentiality_level')->nullable();
            $table->string('photo_url')->nullable();

            // FK exactement comme le script SQL (au minimum celles listées)
            $table->foreign('created_by')->references('id')->on('users');

            $table->foreign('code_province')->references('code_province')->on('provinces');
            $table->foreign('code_territoire')->references('code_territoire')->on('territoires');
            $table->foreign('code_zonesante')->references('code_zonesante')->on('zonesantes');
            // $table->timestamps();

            $table->index('date_incident');
            $table->index('code_province');
            $table->index('statut_incident');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
