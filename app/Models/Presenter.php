<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Presenter extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'biography',
        'photo',
        'instagram',
        'facebook',
        'tiktok',
        'x_twitter',
        'youtube',
        'whatsapp',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function programs()
    {
        return $this->hasMany(Program::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}