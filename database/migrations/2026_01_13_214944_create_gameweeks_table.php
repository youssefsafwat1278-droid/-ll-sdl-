<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gameweeks', function (Blueprint $table) {
            $table->id();
            $table->integer('gameweek_number')->unique();
            $table->string('name', 100)->nullable();
            $table->date('date');
            $table->string('location', 100)->nullable();
            $table->string('photo_url', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamp('deadline');
            $table->boolean('is_current')->default(false);
            $table->boolean('is_finished')->default(false);
            $table->timestamps();

            $table->index('gameweek_number', 'idx_gameweek_number');
            $table->index(['is_current', 'is_finished'], 'idx_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gameweeks');
    }
};
