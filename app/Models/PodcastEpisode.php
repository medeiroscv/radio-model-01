<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PodcastEpisode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'podcast_id',
        'title',
        'slug',
        'description',
        'audio_url',
        'audio_file',
        'duration',
        'image',
        'external_url',
        'published_at',
        'is_published',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_published' => 'boolean',
    ];

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at')->where('published_at', '<=', now());
    }
}