<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'icon_url', 'is_new'];

    protected $casts = [
        'is_new' => 'boolean',
    ];

    public function scopeNew(Builder $query): Builder
    {
        return $query->where('is_new', true)->latest();
    }
}
