<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdImpression extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'banner_id',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function banner()
    {
        return $this->belongsTo(Banner::class);
    }
}