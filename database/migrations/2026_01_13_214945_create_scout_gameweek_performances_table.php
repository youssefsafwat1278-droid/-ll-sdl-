<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scout_gameweek_performances', function (Blueprint $table) {
            $table->id();
            $table->string('scout_id', 10);
            $table->unsignedBigInteger('gameweek_id');

            // 14 Point Categories
            $table->integer('attendance_points')->default(0);
            $table->integer('interaction_points')->default(0);
            $table->integer('uniform_points')->default(0);
            $table->integer('activity_points')->default(0);
            $table->integer('service_points')->default(0);
            $table->integer('committee_points')->default(0);
            $table->integer('mass_points')->default(0);
            $table->integer('confession_points')->default(0);
            $table->integer('group_mass_points')->default(0);
            $table->integer('tribe_mass_points')->default(0);
            $table->integer('aswad_points')->default(0);
            $table->integer('first_group_points')->default(0);
            $table->integer('largest_patrol_points')->default(0);
            $table->integer('penalty_points')->default(0);

            $table->integer('total_points')->default(0);
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->foreign('scout_id')
                ->references('scout_id')
                ->on('scouts')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')
                ->on('gameweeks')
                ->cascadeOnDelete();

            $table->unique(['scout_id', 'gameweek_id'], 'unique_scout_gw');
            $table->index(['scout_id', 'gameweek_id'], 'idx_scout_gameweek');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scout_gameweek_performances');
    }
};
