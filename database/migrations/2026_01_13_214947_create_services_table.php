<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->text('description')->nullable();
            $table->string('icon_url', 255)->nullable();
            $table->boolean('is_new')->default(true);
            $table->timestamps();

            $table->index('is_new', 'idx_is_new');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
