<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\TrackingLog;
use App\Services\GoogleSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncTrackingLogToGoogleSheets implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        public readonly TrackingLog $trackingLog,
    ) {}

    public function handle(GoogleSyncService $service): void
    {
        $service->append($this->trackingLog);
    }
}
