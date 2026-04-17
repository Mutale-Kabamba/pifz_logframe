<?php

declare(strict_types=1);

namespace App\Filament\Resources\TrackingLogResource\Pages;

use App\Filament\Resources\TrackingLogResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateTrackingLog extends CreateRecord
{
    protected static string $resource = TrackingLogResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = Auth::id();

        return $data;
    }
}
