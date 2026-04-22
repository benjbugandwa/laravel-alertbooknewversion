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
        Schema::create('service_providers', function (Blueprint $table) {
            $table->bigIncrements('id'); // integer AUTO PRIMARY KEY
            $table->string('provider_name')->unique();
            $table->string('provider_location')->nullable();
            $table->string('focalpoint_name')->nullable();
            $table->string('focalpoint_email')->nullable();
            $table->string('focalpoint_number')->nullable();

            $table->text('type_services_proposes')->nullable(); // JSON string (multi-select)
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_providers');
    }
};
