<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMemberPoint extends Model
{
    protected $fillable = [
        'user_id',
        'gameweek_id',
        'scout_id',
        'points',
        'is_captain',
        'is_vice_captain',
    ];

    protected $casts = [
        'points' => 'integer',
        'is_captain' => 'boolean',
        'is_vice_captain' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gameweek()
    {
        return $this->belongsTo(Gameweek::class);
    }

    public function scout()
    {
        return $this->belongsTo(Scout::class, 'scout_id', 'scout_id');
    }
}