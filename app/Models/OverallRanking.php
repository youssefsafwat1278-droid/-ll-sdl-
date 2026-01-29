<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OverallRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gameweek_id',
        'overall_rank',
        'gameweek_rank',
        'total_points',
        'gameweek_points',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'gameweek_id' => 'integer',
        'overall_rank' => 'integer',
        'gameweek_rank' => 'integer',
        'total_points' => 'integer',
        'gameweek_points' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class, 'gameweek_id', 'id');
    }
}
