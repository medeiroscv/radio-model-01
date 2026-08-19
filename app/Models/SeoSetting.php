<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SeoSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'page_type',
        'route_name',
        'title',
        'description',
        'canonical',
        'og_image',
        'extra_meta',
    ];
}