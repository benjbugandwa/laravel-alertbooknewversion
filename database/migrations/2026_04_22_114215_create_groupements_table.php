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
        Schema::create('groupements', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();

            $table->string('code_groupement', 20)->primary();
            $table->string('nom_groupement')->unique();
            $table->string('code_chefferie', 20)->nullable();

            $table->foreign('code_chefferie')
                ->references('code_chefferie')
                ->on('chefferies');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groupements');
    }
};
