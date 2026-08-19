<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('legal_name')->nullable();
            $table->string('frequency')->nullable();
            $table->string('slogan')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->default('Brasil');
            $table->string('timezone')->default('America/Sao_Paulo');
            $table->string('website_url')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->text('address')->nullable();
            $table->string('logo_primary')->nullable();
            $table->string('logo_small')->nullable();
            $table->string('favicon')->nullable();
            $table->string('primary_color')->default('#111827');
            $table->string('secondary_color')->default('#374151');
            $table->string('accent_color')->default('#ef4444');
            $table->string('background_color')->default('#ffffff');
            $table->string('surface_color')->default('#f9fafb');
            $table->string('text_color')->default('#111827');
            $table->string('muted_color')->default('#6b7280');
            $table->string('border_color')->default('#e5e7eb');
            $table->string('font_family')->default('Inter');
            $table->string('button_style')->default('rounded');
            $table->boolean('dark_mode_enabled')->default(true);
            $table->boolean('floating_player_enabled')->default(true);
            $table->boolean('is_installed')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stations');
    }
};