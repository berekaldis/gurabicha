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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
            if (!Schema::hasColumn('customers', 'telegram_chat_id')) {
                $table->string('telegram_chat_id')->nullable()->index()->after('phone');
            }
            if (!Schema::hasColumn('customers', 'telegram_username')) {
                $table->string('telegram_username')->nullable()->after('telegram_chat_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            if (Schema::hasColumn('customers', 'telegram_username')) {
                $table->dropColumn('telegram_username');
            }
            if (Schema::hasColumn('customers', 'telegram_chat_id')) {
                $table->dropColumn('telegram_chat_id');
            }
            $table->string('phone')->nullable(false)->change();
        });
    }
};
