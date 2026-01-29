<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gameweek_id');

            $table->string('scout_out_id', 10);
            $table->string('scout_in_id', 10);

            $table->decimal('price_out', 3, 1)->default(9.0);
            $table->decimal('price_in', 3, 1)->default(9.0);

            $table->integer('transfer_cost')->default(0); // -4 per extra transfer

            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')
                ->on('gameweeks')
                ->cascadeOnDelete();

            $table->foreign('scout_out_id')
                ->references('scout_id')
                ->on('scouts')
                ->noActionOnDelete();

            $table->foreign('scout_in_id')
                ->references('scout_id')
                ->on('scouts')
                ->noActionOnDelete();

            $table->index(['user_id', 'gameweek_id'], 'idx_user_gameweek');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
