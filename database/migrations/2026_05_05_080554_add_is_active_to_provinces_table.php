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
        Schema::table('provinces', function (Blueprint $table) {
            $table->string('is_active', 3)->default('YES')->comment('YES or NO');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
