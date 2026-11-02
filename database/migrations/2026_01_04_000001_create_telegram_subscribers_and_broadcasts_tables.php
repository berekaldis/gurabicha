<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('telegram_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->unique()->index();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->nullable()->index();
            $table->string('favorite_club')->nullable()->index();
            $table->string('interest')->default('ALL');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamp('last_interaction_at')->nullable();
            $table->timestamps();
        });

        Schema::create('telegram_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('GENERAL'); // FIXTURE, EVENT, MENU, GENERAL
            $table->string('target_audience')->default('ALL'); // ALL, Arsenal, Chelsea, Man United, Liverpool, ACOUSTIC
            $table->text('message');
            $table->string('action_button_text')->nullable();
            $table->string('action_button_url')->nullable();
            $table->integer('recipients_count')->default(0);
            $table->string('status')->default('SENT'); // SENT, FAILED
            $table->string('sent_by_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_broadcasts');
        Schema::dropIfExists('telegram_subscribers');
    }
};
