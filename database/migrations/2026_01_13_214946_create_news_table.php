<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->text('content');
            $table->string('image_url', 255)->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->foreign('author_id')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->index('is_featured', 'idx_is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};
