<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StreamHistory extends Model
{
    use HasFactory;

    protected $table = 'stream_history';

    protected $fillable = [
        'artist',
        'title',
        'album',
        'cover',
        'played_at',
    ];

    protected $casts = [
        'played_at' => 'datetime',
    ];
}