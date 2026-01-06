<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // 1) Bỏ FK sender_id -> users.id
            // Tên constraint theo log: messages_sender_id_foreign
            $table->dropForeign('messages_sender_id_foreign');

            // 2) Thêm index cho polymorphic sender
            $table->index(['sender_type', 'sender_id'], 'messages_sender_morph_index');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // rollback: bỏ index morph
            $table->dropIndex('messages_sender_morph_index');

            // tạo lại FK về users (trở lại như cũ)
            $table->foreign('sender_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }
};
