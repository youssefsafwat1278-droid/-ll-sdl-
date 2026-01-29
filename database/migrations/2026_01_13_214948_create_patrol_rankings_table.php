<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrol_rankings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('patrol_id');
            $table->unsignedBigInteger('gameweek_id');

            $table->integer('rank');
            $table->integer('total_points');
            $table->integer('gameweek_points')->default(0);
            $table->integer('point_change')->default(0);

            $table->timestamps();

            $table->foreign('patrol_id')
                ->references('patrol_id')->on('patrols')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')->on('gameweeks')
                ->cascadeOnDelete();

            $table->unique(['patrol_id','gameweek_id'], 'unique_patrol_gw');
            $table->index('rank', 'idx_patrol_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrol_rankings');
    }
};
