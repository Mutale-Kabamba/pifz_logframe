<?php

declare(strict_types=1);

namespace App\Observers;

use App\Jobs\SyncTrackingLogToGoogleSheets;
use App\Models\TrackingLog;

class TrackingLogObserver
{
    public function created(TrackingLog $trackingLog): void
    {
        SyncTrackingLogToGoogleSheets::dispatch($trackingLog);
    }

    public function updated(TrackingLog $trackingLog): void
    {
        SyncTrackingLogToGoogleSheets::dispatch($trackingLog);
    }
}
