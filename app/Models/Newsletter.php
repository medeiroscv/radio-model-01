<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Newsletter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'consent',
        'status',
        'unsubscribed_at',
    ];

    protected $casts = [
        'consent' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];
}