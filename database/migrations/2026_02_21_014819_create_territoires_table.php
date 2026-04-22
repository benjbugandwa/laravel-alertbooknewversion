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
        Schema::create('territoires', function (Blueprint $table) {
            // $table->id();
            // $table->timestamps();

            $table->string('code_territoire', 20)->primary();
            $table->string('nom_territoire')->unique();
            $table->string('code_province', 20)->nullable();

            $table->foreign('code_province')
                ->references('code_province')
                ->on('provinces');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('territoires');
    }
};
