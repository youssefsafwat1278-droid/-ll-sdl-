<?php

namespace App\Exports;

use App\Models\Scout;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PointsTemplateExport implements FromCollection, WithHeadings, WithMapping
{
    protected $gameweekId;

    public function __construct($gameweekId)
    {
        $this->gameweekId = $gameweekId;
    }

    public function collection()
    {
        // Get all available scouts ordered by patrol
        return Scout::with('patrol')->orderBy('patrol_id')->orderBy('scout_id')->get();
    }

    public function headings(): array
    {
        return [
            'رقم الكشاف (لا تقم بتعديله)',
            'الاسم (للمرجعية فقط)',
            'الطليعة (للمرجعية فقط)',
            'حضور (2=حاضر، 1=متأخر، -2=غائب)',
            'زي',
            'تفاعل',
            'مشاركة',
            'خدمة',
            'لجان',
            'قداس',
            'اعتراف',
            'قداس مجموعة',
            'قداس قبيلة',
            'أسود',
            'مركز أول',
            'أكبر طليعة',
            'خصومات (بالسالب)',
            'ملاحظات'
        ];
    }

    public function map($scout): array
    {
        // Default points structure
        return [
            $scout->scout_id,
            $scout->full_name,
            $scout->patrol ? $scout->patrol->patrol_name : '',
            2, // Default Attendance
            0, // Default Uniform
            0, // Default Interaction
            0, // Default Activity
            0, // Default Service
            0, // Default Committee
            0, // Default Mass
            0, // Default Confession
            0, // Default Group Mass
            0, // Default Tribe Mass
            0, // Default Aswad
            0, // Default First Group
            0, // Default Largest Patrol
            0, // Default Penalty
            '', // Notes
        ];
    }
}
