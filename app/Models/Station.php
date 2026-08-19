<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'legal_name',
        'frequency',
        'slogan',
        'city',
        'state',
        'country',
        'timezone',
        'website_url',
        'email',
        'phone',
        'whatsapp',
        'address',
        'logo_primary',
        'logo_small',
        'favicon',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'surface_color',
        'text_color',
        'muted_color',
        'border_color',
        'font_family',
        'button_style',
        'dark_mode_enabled',
        'floating_player_enabled',
        'is_installed',
    ];

    protected $casts = [
        'dark_mode_enabled' => 'boolean',
        'floating_player_enabled' => 'boolean',
        'is_installed' => 'boolean',
    ];
}