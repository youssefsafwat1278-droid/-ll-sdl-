<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatrolGameweekPoint extends Model
{
    protected $fillable = [
        'patrol_id',
        'gameweek_id',
        'gameweek_points',
        'total_points_after',
        'rank',
    ];

    protected $casts = [
        'gameweek_points' => 'integer',
        'total_points_after' => 'integer',
        'rank' => 'integer',
    ];

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'patrol_id', 'patrol_id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class);
    }
}