<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StreamSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider_type',
        'stream_url',
        'stream_url_alt',
        'mount_point',
        'username',
        'password',
        'admin_url',
        'stats_url',
        'metadata_url',
        'is_enabled',
        'history_enabled',
        'polling_interval',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'history_enabled' => 'boolean',
        'polling_interval' => 'integer',
    ];
}