<?php

namespace App\Services\Streaming;

use App\Models\Schedule;
use App\Models\StreamHistory;
use App\Models\StreamSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class StreamingService
{
    protected ?StreamingProviderInterface $provider = null;

    protected ?StreamSetting $settings = null;

    public function provider(): StreamingProviderInterface
    {
        if ($this->provider !== null) {
            return $this->provider;
        }

        $settings = $this->settings();
        $provider = StreamingProviderFactory::create($settings->provider_type);
        $provider->configure($settings->only([
            'provider_type',
            'stream_url',
            'stream_url_alt',
            'mount_point',
            'username',
            'password',
            'admin_url',
            'stats_url',
            'metadata_url',
            'api_key',
        ]));

        return $this->provider = $provider;
    }

    public function settings(): StreamSetting
    {
        return $this->settings ??= StreamSetting::firstOrCreate([], ['provider_type' => 'generic']);
    }

    public function isEnabled(): bool
    {
        return $this->settings()->is_enabled && $this->settings()->stream_url;
    }

    public function status(): array
    {
        if (! $this->isEnabled()) {
            return [
                'online' => false,
                'provider' => $this->settings()->provider_type,
                'enabled' => false,
                'message' => 'Streaming não configurado',
            ];
        }

        return Cache::remember('stream-status', now()->addSeconds(20), function () {
            $provider = $this->provider();
            $metadata = $provider->getMetadata();

            $now = Carbon::now();

            return [
                'online' => $provider->isOnline(),
                'provider' => $provider->getName(),
                'enabled' => true,
                'stream_url' => $provider->getStreamUrl(),
                'stream_url_alt' => $provider->getAlternativeStreamUrl(),
                'metadata' => $metadata,
                'listeners' => $provider->getListeners(),
                'now_playing' => $metadata['title'] ? ($metadata['artist'] ? "{$metadata['artist']} - {$metadata['title']}" : $metadata['title']) : null,
                'on_air' => $this->currentProgram(),
                'up_next' => $this->nextPrograms(2),
                'checked_at' => $now->toISOString(),
            ];
        });
    }

    public function currentProgram(): ?array
    {
        $now = Carbon::now();

        $schedule = Schedule::where('is_active', true)
            ->whereRaw('JSON_CONTAINS(days_of_week, ?)', [(string) $now->dayOfWeekIso])
            ->whereTime('start_time', '<=', $now->format('H:i:s'))
            ->whereTime('end_time', '>', $now->format('H:i:s'))
            ->with(['program.presenter', 'presenter'])
            ->first();

        if (! $schedule) {
            return null;
        }

        return [
            'schedule_id' => $schedule->id,
            'program' => $schedule->program?->name,
            'program_slug' => $schedule->program?->slug,
            'presenter' => $schedule->presenter?->name ?? $schedule->program?->presenter?->name,
            'presenter_photo' => $schedule->presenter?->photo ?? $schedule->program?->presenter?->photo,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
        ];
    }

    public function nextPrograms(int $limit = 2): array
    {
        $now = Carbon::now();
        $day = $now->dayOfWeekIso;

        $schedules = Schedule::where('is_active', true)
            ->whereRaw('JSON_CONTAINS(days_of_week, ?)', [(string) $day])
            ->whereTime('start_time', '>', $now->format('H:i:s'))
            ->orderBy('start_time')
            ->with(['program.presenter', 'presenter'])
            ->take($limit)
            ->get();

        return $schedules->map(function ($schedule) {
            return [
                'schedule_id' => $schedule->id,
                'program' => $schedule->program?->name,
                'program_slug' => $schedule->program?->slug,
                'presenter' => $schedule->presenter?->name ?? $schedule->program?->presenter?->name,
                'start_time' => $schedule->start_time,
                'end_time' => $schedule->end_time,
            ];
        })->all();
    }

    public function weeklySchedule(): array
    {
        $schedules = Schedule::where('is_active', true)
            ->with(['program', 'presenter'])
            ->orderBy('start_time')
            ->get();

        $week = [];
        foreach (range(1, 7) as $day) {
            $week[$day] = $schedules->filter(function ($schedule) use ($day) {
                return in_array($day, $schedule->days_of_week ?? []);
            })->values();
        }

        return $week;
    }

    public function saveHistory(array $metadata): void
    {
        if (! $this->settings()->history_enabled) {
            return;
        }

        if (empty($metadata['title']) && empty($metadata['artist'])) {
            return;
        }

        $last = Cache::get('stream-last-track');
        $signature = ($metadata['artist'] ?? '').'|'.($metadata['title'] ?? '');

        if ($last === $signature) {
            return;
        }

        Cache::put('stream-last-track', $signature, now()->addMinutes(60));

        StreamHistory::create([
            'artist' => $metadata['artist'] ?? null,
            'title' => $metadata['title'] ?? null,
            'album' => $metadata['album'] ?? null,
            'cover' => $metadata['cover'] ?? null,
        ]);
    }
}