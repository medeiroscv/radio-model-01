<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChartEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'chart_id',
        'song_id',
        'position',
        'plays',
    ];

    protected $casts = [
        'position' => 'integer',
        'plays' => 'integer',
    ];

    public function chart()
    {
        return $this->belongsTo(Chart::class);
    }

    public function song()
    {
        return $this->belongsTo(Song::class);
    }
}