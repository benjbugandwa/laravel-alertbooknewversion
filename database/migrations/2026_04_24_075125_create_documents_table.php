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
        Schema::create('documents', function (Blueprint $table) {
            $table->uuid('id')->primary();  
           // $table->foreignId('incident_id')->constrained()->onDelete('cascade');
            $table->string('doc_name');
            $table->string('file_path');
            $table->string('mime_type')->nullable();
            $table->string('original_name');
            $table->string('file_type')->nullable();
            $table->string('doc_category')->nullable();
            $table->string('uploaded_by')->nullable();
            $table->text('doc_summary')->nullable();
            $table->integer('download_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
