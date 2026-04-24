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
        Schema::create('chefferies', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();

            $table->string('code_chefferie', 20)->primary();
            $table->string('nom_chefferie')->unique();
            $table->string('code_territoire', 20)->nullable();

            $table->foreign('code_territoire')
                ->references('code_territoire')
                ->on('territoires');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chefferies');
    }
};
