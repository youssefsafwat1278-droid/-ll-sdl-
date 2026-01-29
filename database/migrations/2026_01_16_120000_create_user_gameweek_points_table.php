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
        Schema::create('user_gameweek_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('gameweek_id');
            $table->integer('team_points')->default(0)->comment('نقاط الفريق قبل الخصم (مع مضاعفة الكابتن)');
            $table->integer('transfer_penalty')->default(0)->comment('عقوبة التبديلات الزائدة');
            $table->integer('net_points')->default(0)->comment('النقاط الصافية بعد الخصم');
            $table->integer('total_points_after')->default(0)->comment('الإجمالي التراكمي حتى هذه الجولة');
            $table->integer('rank_in_gameweek')->default(0)->comment('ترتيب المستخدم في الجولة');
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('gameweek_id')->references('id')->on('gameweeks')->onDelete('cascade');

            // Unique constraint: one record per user per gameweek
            $table->unique(['user_id', 'gameweek_id']);

            // Indexes for performance
            $table->index('gameweek_id');
            $table->index('net_points');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_gameweek_points');
    }
};
