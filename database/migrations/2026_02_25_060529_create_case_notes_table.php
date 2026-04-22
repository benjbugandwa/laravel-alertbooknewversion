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
        Schema::create('case_notes', function (Blueprint $table) {
            $table->bigIncrements('id');                 // id auto PRIMARY KEY
            $table->uuid('id_incident')->index();        // incident uuid
            $table->text('case_note');
            $table->boolean('is_confidential')->default(false);
            $table->string('file_path')->nullable();

            $table->unsignedBigInteger('created_by')->index();

            // Pro : on garde created_at + updated_at
            $table->timestamps();

            // FKs (optionnel mais recommandé)
            $table->foreign('id_incident')->references('id')->on('incidents')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_notes');
    }
};
