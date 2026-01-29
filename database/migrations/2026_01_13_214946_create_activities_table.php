<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->string('image_url', 255)->nullable();
            $table->date('activity_date');
            $table->string('location', 100)->nullable();
            $table->timestamps();

            $table->index('activity_date', 'idx_activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
