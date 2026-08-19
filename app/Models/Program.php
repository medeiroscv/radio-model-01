<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'presenter_id',
        'name',
        'slug',
        'description',
        'image',
        'category',
        'social_links',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function presenter()
    {
        return $this->belongsTo(Presenter::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}