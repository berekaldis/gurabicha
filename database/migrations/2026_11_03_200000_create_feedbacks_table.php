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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->string('telegram_chat_id')->nullable()->index();
            $table->string('telegram_username')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('category')->default('GENERAL'); // FOOD, DRINKS, ACOUSTIC, SPORTS, SERVICE, GENERAL
            $table->unsignedTinyInteger('rating')->nullable(); // 1 - 5 stars
            $table->text('message');
            $table->string('status')->default('NEW'); // NEW, REVIEWED, RESOLVED
            $table->string('source')->default('MINIAPP'); // MINIAPP, BOT
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
