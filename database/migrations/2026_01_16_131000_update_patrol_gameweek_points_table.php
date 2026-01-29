<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('patrol_gameweek_points')) {
            return;
        }

        // استخدام SQL مباشر لنقل البيانات وإعادة تسمية الأعمدة
        if (Schema::hasColumn('patrol_gameweek_points', 'points') &&
            !Schema::hasColumn('patrol_gameweek_points', 'gameweek_points')) {

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('gameweek_points')->default(0)->after('gameweek_id');
            });

            DB::statement('UPDATE patrol_gameweek_points SET gameweek_points = points');

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->dropColumn('points');
            });
        } elseif (!Schema::hasColumn('patrol_gameweek_points', 'gameweek_points') &&
                  !Schema::hasColumn('patrol_gameweek_points', 'points')) {
            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('gameweek_points')->default(0)->after('gameweek_id');
            });
        }

        // نفس الشيء مع rank
        if (Schema::hasColumn('patrol_gameweek_points', 'rank_in_gameweek') &&
            !Schema::hasColumn('patrol_gameweek_points', 'rank')) {

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('rank')->default(0)->after('total_points_after');
            });

            DB::statement('UPDATE patrol_gameweek_points SET rank = rank_in_gameweek');

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->dropColumn('rank_in_gameweek');
            });
        } elseif (!Schema::hasColumn('patrol_gameweek_points', 'rank') &&
                  !Schema::hasColumn('patrol_gameweek_points', 'rank_in_gameweek')) {
            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('rank')->default(0)->after('total_points_after');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('patrol_gameweek_points')) {
            return;
        }

        // عكس العملية
        if (Schema::hasColumn('patrol_gameweek_points', 'gameweek_points')) {
            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('points')->default(0)->after('gameweek_id');
            });

            DB::statement('UPDATE patrol_gameweek_points SET points = gameweek_points');

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->dropColumn('gameweek_points');
            });
        }

        if (Schema::hasColumn('patrol_gameweek_points', 'rank')) {
            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->integer('rank_in_gameweek')->default(0)->after('total_points_after');
            });

            DB::statement('UPDATE patrol_gameweek_points SET rank_in_gameweek = rank');

            Schema::table('patrol_gameweek_points', function (Blueprint $table) {
                $table->dropColumn('rank');
            });
        }
    }
};

