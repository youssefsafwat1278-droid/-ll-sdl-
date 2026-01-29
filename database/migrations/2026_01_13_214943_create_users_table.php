<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('scout_id', 10)->unique();
            $table->string('password', 255);

            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->unsignedBigInteger('patrol_id')->nullable();
            $table->string('photo_url', 255)->nullable();
            $table->string('team_name', 50)->default('فريقي');

            // Budget & Points
            $table->decimal('bank_balance', 4, 1)->default(100.0);
            $table->integer('total_points')->default(0);
            $table->integer('gameweek_points')->default(0);

            // Transfers & Chips
            $table->integer('free_transfers')->default(3);
            $table->integer('triple_captain_used')->default(0);
            $table->boolean('bench_boost_used')->default(false);
            $table->boolean('free_hit_used')->default(false);

            // Settings
            $table->enum('theme', ['light', 'dark'])->default('light');
            $table->enum('language', ['ar', 'en'])->default('ar');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('profile_public')->default(true);

            // Role
            $table->enum('role', ['user', 'admin'])->default('user');

            $table->timestamp('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('scout_id')
                ->references('scout_id')
                ->on('scouts')
                ->cascadeOnDelete();

            $table->foreign('patrol_id')
                ->references('patrol_id')
                ->on('patrols')
                ->nullOnDelete();

            $table->index('scout_id', 'idx_scout_id');
            $table->index('patrol_id', 'idx_patrol_id');
            $table->index('total_points', 'idx_total_points');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
