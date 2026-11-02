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
        Schema::create('venue_tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('zone');
            $table->integer('capacity');
            $table->string('seats');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->text('description');
            $table->string('category');
            $table->dateTime('date');
            $table->string('startTime');
            $table->string('endTime');
            $table->string('imageUrl')->nullable();
            $table->boolean('featured')->default(false);
            $table->timestamps();

            $table->index('date');
        });

        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('homeTeam');
            $table->string('awayTeam');
            $table->string('competition');
            $table->string('season')->default('');
            $table->string('stage')->default('');
            $table->string('venue')->nullable();
            $table->dateTime('kickoff');
            $table->string('status')->default('SCHEDULED');
            $table->integer('homeScore')->nullable();
            $table->integer('awayScore')->nullable();
            $table->boolean('featured')->default(false);
            $table->boolean('live')->default(false);
            $table->timestamps();

            $table->index('kickoff');
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->text('description');
            $table->boolean('popular')->default(false);
            $table->timestamps();

            $table->index('category');
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->dateTime('date');
            $table->string('slot');
            $table->string('tableCode');
            $table->integer('guests');
            $table->string('name');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('request')->nullable();
            $table->string('matchId')->nullable();
            $table->string('matchLabel')->nullable();
            $table->string('telegramUserId')->nullable();
            $table->string('telegramUsername')->nullable();
            $table->string('status')->default('CONFIRMED');
            $table->timestamps();

            $table->index(['date', 'slot', 'tableCode']);
            $table->index('phone');
            $table->index('telegramUserId');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('matches');
        Schema::dropIfExists('events');
        Schema::dropIfExists('venue_tables');
    }
};
