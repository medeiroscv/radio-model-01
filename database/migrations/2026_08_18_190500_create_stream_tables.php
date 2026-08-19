<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stream_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider_type')->default('generic');
            $table->string('stream_url')->nullable();
            $table->string('stream_url_alt')->nullable();
            $table->string('mount_point')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('admin_url')->nullable();
            $table->string('stats_url')->nullable();
            $table->string('metadata_url')->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->boolean('history_enabled')->default(true);
            $table->integer('polling_interval')->default(30);
            $table->timestamps();
        });

        Schema::create('stream_history', function (Blueprint $table) {
            $table->id();
            $table->string('artist')->nullable();
            $table->string('title')->nullable();
            $table->string('album')->nullable();
            $table->string('cover')->nullable();
            $table->timestamp('played_at')->useCurrent();

            $table->index('played_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stream_history');
        Schema::dropIfExists('stream_settings');
    }
};