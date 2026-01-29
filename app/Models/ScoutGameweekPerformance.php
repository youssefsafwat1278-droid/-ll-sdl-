<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoutGameweekPerformance extends Model
{
    use HasFactory;

    protected $fillable = [
        'scout_id',
        'gameweek_id',
        'attendance_points',
        'interaction_points',
        'uniform_points',
        'activity_points',
        'service_points',
        'committee_points',
        'mass_points',
        'confession_points',
        'group_mass_points',
        'tribe_mass_points',
        'aswad_points',
        'first_group_points',
        'largest_patrol_points',
        'penalty_points',
        'total_points',
        'notes',
    ];

    protected $casts = [
        'gameweek_id' => 'integer',
        'attendance_points' => 'integer',
        'interaction_points' => 'integer',
        'uniform_points' => 'integer',
        'activity_points' => 'integer',
        'service_points' => 'integer',
        'committee_points' => 'integer',
        'mass_points' => 'integer',
        'confession_points' => 'integer',
        'group_mass_points' => 'integer',
        'tribe_mass_points' => 'integer',
        'aswad_points' => 'integer',
        'first_group_points' => 'integer',
        'largest_patrol_points' => 'integer',
        'penalty_points' => 'integer',
        'total_points' => 'integer',
    ];

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }

    public function calculateTotal(): int
    {
        return (int)(
            $this->attendance_points +
            $this->interaction_points +
            $this->uniform_points +
            $this->activity_points +
            $this->service_points +
            $this->committee_points +
            $this->mass_points +
            $this->confession_points +
            $this->group_mass_points +
            $this->tribe_mass_points +
            $this->aswad_points +
            $this->first_group_points +
            $this->largest_patrol_points +
            $this->penalty_points
        );
    }
}
