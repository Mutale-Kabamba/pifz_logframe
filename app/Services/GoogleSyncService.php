<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TrackingLog;
use Illuminate\Support\Facades\Log;
use Revolution\Google\Sheets\Facades\Sheets;

class GoogleSyncService
{
    public function append(TrackingLog $trackingLog): void
    {
        $trackingLog->loadMissing(['logframeItem.project', 'recorder']);

        $logframeItem = $trackingLog->logframeItem;
        $project = $logframeItem->project;

        if (! $project->spreadsheet_id) {
            Log::warning('Google Sheets sync skipped: no spreadsheet_id for project', [
                'project_id' => $project->id,
            ]);

            return;
        }

        $row = [
            $project->name,
            $logframeItem->category->label(),
            $logframeItem->indicator ?? '',
            $trackingLog->actual_value,
            $trackingLog->recorder->name ?? 'Unknown',
            $trackingLog->recorded_at->toDateTimeString(),
            $trackingLog->evidence_link ?? '',
            $trackingLog->notes ?? '',
        ];

        try {
            Sheets::spreadsheet($project->spreadsheet_id)
                ->sheet('Tracking Data')
                ->append([$row]);

            Log::info('Tracking log synced to Google Sheets', [
                'tracking_log_id' => $trackingLog->id,
                'project_id' => $project->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Google Sheets sync failed', [
                'tracking_log_id' => $trackingLog->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
