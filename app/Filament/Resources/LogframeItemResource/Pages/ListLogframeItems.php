<?php

declare(strict_types=1);

namespace App\Filament\Resources\LogframeItemResource\Pages;

use App\Filament\Resources\LogframeItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogframeItems extends ListRecords
{
    protected static string $resource = LogframeItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
