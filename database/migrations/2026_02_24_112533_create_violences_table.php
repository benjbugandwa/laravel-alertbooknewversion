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
        Schema::create('violences', function (Blueprint $table) {
            $table->integer('id')->primary(); // script: integer PRIMARY KEY
            $table->string('violence_name')->unique();
            $table->string('categorie_name')->nullable();
            $table->text('violence_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('violences');
    }
};
