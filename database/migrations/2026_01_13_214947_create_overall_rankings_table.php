<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overall_rankings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gameweek_id');

            $table->integer('overall_rank');
            $table->integer('gameweek_rank')->nullable();
            $table->integer('total_points');
            $table->integer('gameweek_points');

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')->on('gameweeks')
                ->cascadeOnDelete();

            $table->unique(['user_id','gameweek_id'], 'unique_user_gw');
            $table->index('overall_rank', 'idx_overall_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overall_rankings');
    }
};
