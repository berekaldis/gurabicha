<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add role & must_change_password to users
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('SUPER_ADMIN')->after('password');
            $table->boolean('must_change_password')->default(false)->after('role');
            $table->string('status')->default('ACTIVE')->after('must_change_password');
        });

        // 2. Create customers table for club fan notifications & customer info
        Schema::create('customers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable();
            $table->string('favorite_club')->nullable();
            $table->boolean('notify_matchday')->default(true);
            $table->boolean('notify_scores')->default(true);
            $table->boolean('notify_acoustic')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'must_change_password', 'status']);
        });
    }
};
