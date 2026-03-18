<?php

namespace App\Imports;

use App\Models\Scout;
use App\Models\ScoutGameweekPerformance;
use App\Models\Gameweek;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class PointsImport implements ToCollection, WithStartRow
{
    protected $gameweekId;
    protected $imported;
    protected $errors;

    public function __construct($gameweekId, &$imported, &$errors)
    {
        $this->gameweekId = $gameweekId;
        $this->imported = &$imported;
        $this->errors = &$errors;
    }

    public function startRow(): int
    {
        return 2; // Skip headings row
    }

    public function collection(Collection $rows)
    {
        $gameweek = Gameweek::find($this->gameweekId);
        
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // For error messages
            
            $scoutId = $row[0] ?? null;

            if (empty($scoutId)) {
                continue;
            }

            $scout = Scout::find($scoutId);
            if (!$scout) {
                $this->errors[] = "الصف {$rowNum}: الكشاف غير موجود ({$scoutId})";
                continue;
            }

            $attendance = (int)($row[3] ?? 2);
            $uniform = (int)($row[4] ?? 0);
            $interaction = (int)($row[5] ?? 0);
            $activity = (int)($row[6] ?? 0);
            $service = (int)($row[7] ?? 0);
            $committee = (int)($row[8] ?? 0);
            $mass = (int)($row[9] ?? 0);
            $confession = (int)($row[10] ?? 0);
            $group_mass = (int)($row[11] ?? 0);
            $tribe_mass = (int)($row[12] ?? 0);
            $aswad = (int)($row[13] ?? 0);
            $first_group = (int)($row[14] ?? 0);
            $largest_patrol = (int)($row[15] ?? 0);
            $penalty = (int)($row[16] ?? 0);
            $notes = $row[17] ?? null;

            $totalPoints = $attendance + $uniform + $interaction + $activity + 
                           $service + $committee + $mass + $confession + 
                           $group_mass + $tribe_mass + $aswad + $first_group + 
                           $largest_patrol + $penalty;

            ScoutGameweekPerformance::updateOrCreate(
                [
                    'scout_id' => $scoutId,
                    'gameweek_id' => $this->gameweekId,
                ],
                [
                    'attendance_points' => $attendance,
                    'uniform_points' => $uniform,
                    'interaction_points' => $interaction,
                    'activity_points' => $activity,
                    'service_points' => $service,
                    'committee_points' => $committee,
                    'mass_points' => $mass,
                    'confession_points' => $confession,
                    'group_mass_points' => $group_mass,
                    'tribe_mass_points' => $tribe_mass,
                    'aswad_points' => $aswad,
                    'first_group_points' => $first_group,
                    'largest_patrol_points' => $largest_patrol,
                    'penalty_points' => $penalty,
                    'total_points' => $totalPoints,
                    'notes' => $notes,
                ]
            );

            if ($gameweek && $gameweek->is_current) {
                $scout->update(['gameweek_points' => $totalPoints]);
            }

            $this->imported++;
        }
    }
}
