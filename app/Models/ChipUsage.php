<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChipUsage extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'gameweek_id', 'chip_type'];

    protected $casts = [
        'user_id' => 'integer',
        'gameweek_id' => 'integer',
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
