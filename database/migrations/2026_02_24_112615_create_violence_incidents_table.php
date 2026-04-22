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
        Schema::create('violence_incidents', function (Blueprint $table) {
            $table->integer('id')->primary(); // script: integer PRIMARY KEY

            $table->uuid('id_incident')->nullable();   // incident uuid
            $table->integer('id_violence')->nullable(); // violence id

            $table->text('description_violence')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->nullable();

            // Index utiles
            $table->index('id_incident');
            $table->index('id_violence');

            // Un incident ne doit pas avoir 2 fois le même type de violence
            $table->unique(['id_incident', 'id_violence'], 'violences_incident_unique_pair');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violence_incidents');
    }
};
