<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scouts', function (Blueprint $table) {
            $table->integer('local_ownership_count')->default(0)->after('ownership_count');
            $table->integer('external_ownership_count')->default(0)->after('local_ownership_count');
        });

        $currentGameweekId = DB::table('gameweeks')
            ->where('is_current', true)
            ->value('id');

        if (!$currentGameweekId) {
            return;
        }

        $totalCounts = DB::table('user_teams')
            ->where('gameweek_id', $currentGameweekId)
            ->select('scout_id', DB::raw('count(*) as cnt'))
            ->groupBy('scout_id')
            ->pluck('cnt', 'scout_id');

        $localCounts = DB::table('user_teams')
            ->join('users', 'user_teams.user_id', '=', 'users.id')
            ->join('scouts', 'user_teams.scout_id', '=', 'scouts.scout_id')
            ->where('user_teams.gameweek_id', $currentGameweekId)
            ->whereColumn('users.patrol_id', 'scouts.patrol_id')
            ->select('scouts.scout_id', DB::raw('count(*) as cnt'))
            ->groupBy('scouts.scout_id')
            ->pluck('cnt', 'scouts.scout_id');

        $externalCounts = DB::table('user_teams')
            ->join('users', 'user_teams.user_id', '=', 'users.id')
            ->join('scouts', 'user_teams.scout_id', '=', 'scouts.scout_id')
            ->where('user_teams.gameweek_id', $currentGameweekId)
            ->where(function ($query) {
                $query->whereColumn('users.patrol_id', '!=', 'scouts.patrol_id')
                    ->orWhereNull('users.patrol_id')
                    ->orWhereNull('scouts.patrol_id');
            })
            ->select('scouts.scout_id', DB::raw('count(*) as cnt'))
            ->groupBy('scouts.scout_id')
            ->pluck('cnt', 'scouts.scout_id');

        $scouts = DB::table('scouts')->select('scout_id', 'role')->get();

        foreach ($scouts as $scout) {
            $total = (int) ($totalCounts[$scout->scout_id] ?? 0);
            $local = (int) ($localCounts[$scout->scout_id] ?? 0);
            $external = (int) ($externalCounts[$scout->scout_id] ?? 0);

            $isLeader = in_array($scout->role, ['leader', 'senior'], true);
            $isAvailable = $isLeader ? $total < 20 : ($local < 3 || $external < 5);

            DB::table('scouts')
                ->where('scout_id', $scout->scout_id)
                ->update([
                    'ownership_count' => $total,
                    'local_ownership_count' => $local,
                    'external_ownership_count' => $external,
                    'is_available' => $isAvailable,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('scouts', function (Blueprint $table) {
            $table->dropColumn(['local_ownership_count', 'external_ownership_count']);
        });
    }
};
