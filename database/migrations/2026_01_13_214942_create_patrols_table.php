<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patrols', function (Blueprint $table) {
            $table->id('patrol_id');
            $table->string('patrol_name', 50)->unique();
            $table->string('patrol_logo_url', 255)->nullable();
            $table->string('patrol_color', 7)->nullable(); // #1E40AF
            $table->integer('total_points')->default(0);
            $table->integer('rank')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('rank', 'idx_rank');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patrols');
    }
};
