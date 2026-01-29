<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatrolRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'patrol_id',
        'gameweek_id',
        'rank',
        'total_points',
        'gameweek_points',
        'point_change',
    ];

    protected $casts = [
        'patrol_id' => 'integer',
        'gameweek_id' => 'integer',
        'rank' => 'integer',
        'total_points' => 'integer',
        'gameweek_points' => 'integer',
        'point_change' => 'integer',
    ];

    public function patrol()
    {
        return $this->belongsTo(Patrol::class, 'patrol_id', 'patrol_id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }
}
