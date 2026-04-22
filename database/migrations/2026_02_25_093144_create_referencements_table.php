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
        Schema::create('referencements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code_referencement')->unique();

            $table->uuid('id_incident')->index();
            $table->timestamp('date_referencement');
            $table->unsignedBigInteger('provider_id')->index();

            $table->text('resultat')->nullable();
            $table->string('type_reponse')->nullable();
            $table->string('statut_reponse')->nullable();

            $table->boolean('besoin_suivi')->default(false);
            $table->text('observations')->nullable();
            $table->string('file_path')->nullable();

            $table->unsignedBigInteger('created_by')->index();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('referencements');
    }
};
