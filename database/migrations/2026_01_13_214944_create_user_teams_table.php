<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_teams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gameweek_id');
            $table->string('scout_id', 10);

            $table->integer('position_in_squad'); // 0-10
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_vice_captain')->default(false);

            $table->decimal('purchase_price', 3, 1)->default(9.0);
            $table->decimal('current_price', 3, 1)->default(9.0);

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')
                ->on('gameweeks')
                ->cascadeOnDelete();

            $table->foreign('scout_id')
                ->references('scout_id')
                ->on('scouts')
                ->noActionOnDelete();

            $table->unique(['user_id', 'scout_id', 'gameweek_id'], 'unique_user_scout_gw');
            $table->index(['user_id', 'gameweek_id'], 'idx_user_gameweek');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_teams');
    }
};
