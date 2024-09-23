<?php

use App\Models\Dialog;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sender_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('receiver_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('chat_id')
                ->constrained('chats')
                ->cascadeOnDelete();

            $table->foreignIdFor(Dialog::class)
                ->constrained('dialogs')
                ->cascadeOnDelete();

            $table->text('content');
            $table->enum('type', ['text', 'file'])->default('text');
            $table->boolean('is_read')->default(false);
            $table->boolean('is_sent')->default(false);

            $table->index('sender_id');
            $table->index('receiver_id');
            $table->index('chat_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
