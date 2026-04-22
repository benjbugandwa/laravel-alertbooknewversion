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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email');
            $table->string('user_role', 50)->nullable()->after('phone_number');
            $table->boolean('is_active')->default(true)->after('user_role');
            $table->unsignedBigInteger('org_id')->nullable()->after('is_active');
            $table->string('avatar_url', 200)->nullable()->after('org_id');
            $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            $table->text('two_factor_secret')->nullable()->after('last_login_at');
            $table->string('code_province', 3)->nullable()->after('two_factor_secret');

            $table->index('org_id');
            $table->foreign('org_id')->references('id')->on('organisations')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
