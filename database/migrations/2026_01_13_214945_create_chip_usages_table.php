<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chip_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gameweek_id');
            $table->enum('chip_type', ['triple_captain', 'bench_boost', 'free_hit']);
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')
                ->on('gameweeks')
                ->cascadeOnDelete();

            $table->index(['user_id', 'gameweek_id'], 'idx_user_gameweek');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chip_usages');
    }
};
