<?php

declare(strict_types=1);

namespace App\Filament\Resources\LogframeItemResource\Pages;

use App\Filament\Resources\LogframeItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLogframeItem extends CreateRecord
{
    protected static string $resource = LogframeItemResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
