<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('price_histories', function (Blueprint $table) {
            $table->id();
            $table->string('scout_id', 10);
            $table->unsignedBigInteger('gameweek_id');

            $table->decimal('price_before', 3, 1);
            $table->decimal('price_after', 3, 1);
            $table->decimal('price_change', 2, 1);

            $table->integer('ownership_count');
            $table->integer('previous_ownership_count');
            $table->decimal('ownership_average', 3, 1);

            $table->string('reason', 50)->nullable();
            $table->timestamps();

            $table->foreign('scout_id')
                ->references('scout_id')->on('scouts')
                ->cascadeOnDelete();

            $table->foreign('gameweek_id')
                ->references('id')->on('gameweeks')
                ->cascadeOnDelete();

            $table->index(['scout_id','gameweek_id'], 'idx_price_scout_gw');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('price_histories');
    }
};
