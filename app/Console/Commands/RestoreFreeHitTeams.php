<?php

namespace App\Console\Commands;

use App\Models\ChipUsage;
use App\Models\FreeHitSnapshot;
use App\Models\Gameweek;
use App\Models\UserTeam;
use App\Models\Scout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RestoreFreeHitTeams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'freehit:restore {--gameweek_id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'إرجاع الفرق إلى حالتها الأصلية بعد انتهاء جولة Free Hit';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $gameweekId = $this->option('gameweek_id');

        if (!$gameweekId) {
            // البحث عن الجولة السابقة التي انتهت
            $previousGameweek = Gameweek::where('is_current', false)
                ->orderBy('gameweek_number', 'desc')
                ->first();

            if (!$previousGameweek) {
                $this->error('لا توجد جولة سابقة للمعالجة.');
                return 1;
            }

            $gameweekId = $previousGameweek->id;
        }

        $this->info("معالجة Free Hit للجولة: {$gameweekId}");

        // العثور على جميع المستخدمين الذين فعلوا Free Hit في هذه الجولة
        $freeHitUsers = ChipUsage::where('gameweek_id', $gameweekId)
            ->where('chip_type', 'free_hit')
            ->pluck('user_id');

        if ($freeHitUsers->isEmpty()) {
            $this->info('لا يوجد مستخدمون فعّلوا Free Hit في هذه الجولة.');
            return 0;
        }

        $this->info("عدد المستخدمين: {$freeHitUsers->count()}");

        DB::beginTransaction();
        try {
            foreach ($freeHitUsers as $userId) {
                $this->restoreTeamForUser($userId, $gameweekId);
            }

            DB::commit();
            $this->info('تم إرجاع جميع الفرق بنجاح!');
            return 0;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('حدث خطأ: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * إرجاع الفريق الأصلي لمستخدم معين
     */
    private function restoreTeamForUser(int $userId, int $gameweekId): void
    {
        $this->info("إرجاع الفريق للمستخدم: {$userId}");

        // الحصول على النسخة الاحتياطية من الفريق الأصلي
        $originalTeam = FreeHitSnapshot::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->get();

        if ($originalTeam->isEmpty()) {
            $this->warn("لا توجد نسخة احتياطية للمستخدم {$userId}");
            return;
        }

        // الحصول على الجولة التالية (الجولة الحالية بعد انتهاء Free Hit)
        $nextGameweek = Gameweek::where('gameweek_number', '>',
            Gameweek::find($gameweekId)->gameweek_number)
            ->orderBy('gameweek_number', 'asc')
            ->first();

        if (!$nextGameweek) {
            $this->warn("لا توجد جولة تالية للمستخدم {$userId}");
            return;
        }

        // حذف الفريق المؤقت في الجولة التالية
        UserTeam::where('user_id', $userId)
            ->where('gameweek_id', $nextGameweek->id)
            ->delete();

        // إرجاع الفريق الأصلي إلى الجولة التالية
        foreach ($originalTeam as $player) {
            $scout = Scout::find($player->scout_id);

            UserTeam::create([
                'user_id' => $userId,
                'gameweek_id' => $nextGameweek->id,
                'scout_id' => $player->scout_id,
                'position_in_squad' => $player->position_in_squad,
                'is_captain' => $player->is_captain,
                'is_vice_captain' => $player->is_vice_captain,
                'purchase_price' => $player->purchase_price ?? ($scout ? $scout->current_price : 9.0),
                'current_price' => $scout ? $scout->current_price : 9.0,
            ]);
        }

        // حذف النسخة الاحتياطية بعد الاستخدام
        FreeHitSnapshot::where('user_id', $userId)
            ->where('gameweek_id', $gameweekId)
            ->delete();

        $this->info("تم إرجاع الفريق للمستخدم {$userId} بنجاح");
    }
}
