<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['price_alert','deadline','news','ranking','other']);
            $table->string('title', 200);
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->index(['user_id','is_read'], 'idx_user_unread');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
