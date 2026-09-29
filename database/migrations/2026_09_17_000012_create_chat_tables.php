<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Chat Bot Rules
        Schema::create('chat_bot_rules', function (Blueprint $table) {
            $table->id('rule_id');
            $table->string('rule_name', 150);
            $table->text('keywords');
            $table->enum('match_type', ['CONTAINS', 'EXACT', 'REGEX'])->default('CONTAINS');
            $table->text('response_message');
            $table->json('quick_replies')->nullable();
            $table->enum('action_type', ['REPLY', 'HANDOVER_STAFF'])->default('REPLY');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Chat Conversations
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 100)->index();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('customer_name')->default('Khách hàng CurtainLux');
            $table->string('customer_phone', 20)->nullable();
            $table->enum('status', ['bot', 'waiting_staff', 'staff_connected', 'closed'])->default('bot');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        // 3. Chat Messages
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->onDelete('cascade');
            $table->enum('sender_type', ['customer', 'bot', 'staff'])->default('customer');
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message');
            $table->json('quick_replies')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('chat_bot_rules');
    }
};
