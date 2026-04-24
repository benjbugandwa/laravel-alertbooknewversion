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
        Schema::create('mouvements', function (Blueprint $table) {
            $table->id();            
            $table->date('date_mouvement');
            $table->enum('type_mouvement',['Fuite','Retour']); //Fuite ou Retour
            $table->string('source_info');
            $table->string('code_province_prov');
            $table->string('code_territoire_prov');
            $table->string('code_zonesante_prov')->nullable();
            $table->string('localite_prov');
            
            $table->string('code_province_accl');
            $table->string('code_territoire_accl');
            $table->string('code_zonesante_accl')->nullable();
            $table->string('localite_accl');

            $table->enum('type_logement',['Site spontané','Centre collectif','Famille accueil','Autre'])->nullable();
            $table->unsignedBigInteger('created_by');

            $table->unsignedBigInteger('estim_nbre_menages')->nullable();
            $table->unsignedBigInteger('estim_nbre_personnes')->nullable();

            $table->text('remarques_mouvement')->nullable();
            
            $table->uuid('incident_id');  
            $table->foreign('incident_id')->references('id')->on('incidents')->onDelete('cascade');            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mouvements');
    }
};
