<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('period', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('chart_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('song_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->index();
            $table->integer('plays')->default(0);
            $table->timestamps();

            $table->unique(['chart_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chart_entries');
        Schema::dropIfExists('charts');
    }
};