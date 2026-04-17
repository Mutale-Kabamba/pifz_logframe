<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\LogframeItem;
use App\Models\Project;
use App\Models\TrackingLog;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class LogframeDashboardStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        /** @var User $user */
        $user = Auth::user();

        $projectQuery = Project::query();
        if ($user->hasRole('project_officer')) {
            $projectQuery->where('assigned_officer_id', $user->id);
        }

        $projectCount = $projectQuery->count();
        $projectIds = $projectQuery->pluck('id');

        $logframeCount = LogframeItem::whereIn('project_id', $projectIds)->count();
        $trackingLogCount = TrackingLog::whereHas('logframeItem', fn ($q) => $q->whereIn('project_id', $projectIds))->count();

        return [
            Stat::make('Total Projects', (string) $projectCount)
                ->icon('heroicon-o-briefcase')
                ->color('primary'),
            Stat::make('Logframe Items', (string) $logframeCount)
                ->icon('heroicon-o-table-cells')
                ->color('success'),
            Stat::make('Tracking Entries', (string) $trackingLogCount)
                ->icon('heroicon-o-chart-bar')
                ->color('warning'),
        ];
    }
}
