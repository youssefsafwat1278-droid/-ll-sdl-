<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scouts', function (Blueprint $table) {
            $table->string('scout_id', 10)->primary(); // SC001
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->unsignedBigInteger('patrol_id')->nullable();
            $table->enum('role', ['scout', 'leader', 'senior']);
            $table->string('photo_url', 255)->nullable();

            // Pricing
            $table->decimal('initial_price', 3, 1)->default(9.0);
            $table->decimal('current_price', 3, 1)->default(9.0);
            $table->decimal('price_change', 2, 1)->default(0.0);
            $table->string('price_trend', 10)->default('stable'); // rising/falling/stable

            // Performance
            $table->integer('total_points')->default(0);
            $table->integer('gameweek_points')->default(0);
            $table->decimal('form', 3, 1)->default(0.0); // avg last 5

            // Ownership
            $table->integer('ownership_count')->default(0); // max 5
            $table->integer('previous_ownership_count')->default(0);
            $table->decimal('ownership_percentage', 5, 2)->default(0.0);
            $table->decimal('ownership_average', 3, 1)->default(0.0);

            // Availability
            $table->boolean('is_available')->default(true);
            $table->enum('status', ['available', 'injured', 'suspended'])->default('available');

            $table->timestamps();

            $table->foreign('patrol_id')
                ->references('patrol_id')
                ->on('patrols')
                ->nullOnDelete();

            $table->index('patrol_id', 'idx_patrol');
            $table->index(['ownership_count', 'is_available'], 'idx_ownership');
            $table->index(['price_trend', 'current_price'], 'idx_price_trend_price');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scouts');
    }
};
