<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('chat_conversations') && !Schema::hasColumn('chat_conversations', 'bot_unmatched_count')) {
            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->integer('bot_unmatched_count')->default(0)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('chat_conversations') && Schema::hasColumn('chat_conversations', 'bot_unmatched_count')) {
            Schema::table('chat_conversations', function (Blueprint $table) {
                $table->dropColumn('bot_unmatched_count');
            });
        }
    }
};
