<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('patrol_gameweek_points')) {
            return;
        }

        Schema::create('patrol_gameweek_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patrol_id');
            $table->unsignedBigInteger('gameweek_id');
            $table->integer('gameweek_points')->default(0);
            $table->integer('total_points_after')->default(0);
            $table->integer('rank')->default(0);
            $table->timestamps();

            $table->foreign('patrol_id')
                ->references('patrol_id')
                ->on('patrols')
                ->noActionOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')
                ->on('gameweeks')
                ->noActionOnDelete();

            $table->unique(['patrol_id', 'gameweek_id'], 'unique_patrol_gameweek_points');
            $table->index(['gameweek_id', 'rank'], 'idx_patrol_gw_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_gameweek_points');
    }
};
