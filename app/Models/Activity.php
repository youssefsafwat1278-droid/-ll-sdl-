<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'image_url', 'activity_date', 'location'];

    protected $casts = [
        'activity_date' => 'date',
    ];

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('activity_date', '>=', now()->toDateString())->orderBy('activity_date');
    }
}
