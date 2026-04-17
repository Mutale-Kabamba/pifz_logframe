<?php

declare(strict_types=1);

namespace App\Filament\Resources\TrackingLogResource\Pages;

use App\Filament\Resources\TrackingLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTrackingLog extends EditRecord
{
    protected static string $resource = TrackingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
