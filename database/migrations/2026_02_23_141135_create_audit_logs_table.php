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
        Schema::create('audit_logs', function (Blueprint $table) {
            // Ton script : id integer PRIMARY KEY
            $table->bigIncrements('id')->primary();

            // FK utilisateur (users.id integer)
            $table->unsignedBigInteger('user_id')->nullable();

            $table->string('user_action')->nullable();
            $table->string('model_type')->nullable();
            $table->integer('model_id')->nullable();

            // CORRECTION RECOMMANDÉE
            $table->string('ip_address', 45)->nullable();
            // 45 caractères = IPv6 max

            $table->text('action_meta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
