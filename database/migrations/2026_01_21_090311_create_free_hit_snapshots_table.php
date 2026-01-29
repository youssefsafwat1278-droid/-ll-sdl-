<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('free_hit_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('gameweek_id')->constrained('gameweeks')->onDelete('cascade');
            $table->string('scout_id', 10);
            $table->enum('position_in_squad', [
                'goalkeeper', 'defender1', 'defender2', 'defender3',
                'midfielder1', 'midfielder2', 'midfielder3',
                'forward1', 'forward2', 'forward3', 'bench'
            ]);
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_vice_captain')->default(false);
            $table->timestamps();

            $table->foreign('scout_id')->references('scout_id')->on('scouts')->onDelete('no action');
            $table->index(['user_id', 'gameweek_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('free_hit_snapshots');
    }
};
