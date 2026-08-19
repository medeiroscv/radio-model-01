<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Song extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'artist_id',
        'title',
        'slug',
        'cover',
        'description',
        'spotify_url',
        'youtube_url',
        'deezer_url',
        'apple_music_url',
        'external_url',
        'is_featured',
        'is_release',
        'released_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_release' => 'boolean',
        'released_at' => 'datetime',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function chartEntries()
    {
        return $this->hasMany(ChartEntry::class);
    }
}