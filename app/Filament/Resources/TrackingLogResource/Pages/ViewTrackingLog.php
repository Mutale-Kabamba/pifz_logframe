<?php

declare(strict_types=1);

namespace App\Filament\Resources\TrackingLogResource\Pages;

use App\Filament\Resources\TrackingLogResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewTrackingLog extends ViewRecord
{
    protected static string $resource = TrackingLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Tracking Entry Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('logframeItem.project.name')
                            ->label('Project'),
                        TextEntry::make('logframeItem.category')
                            ->label('Category')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => $state->label())
                            ->color(fn ($state): string => $state->color()),
                        TextEntry::make('logframeItem.description')
                            ->label('Logframe Item')
                            ->columnSpanFull(),
                        TextEntry::make('actual_value')
                            ->label('Actual / Registered Value')
                            ->weight('bold'),
                        TextEntry::make('recorded_at')
                            ->label('Date Recorded')
                            ->dateTime(),
                        TextEntry::make('recorder.name')
                            ->label('Recorded By'),
                        TextEntry::make('evidence_link')
                            ->label('Evidence Link')
                            ->url(fn (?string $state): ?string => $state)
                            ->openUrlInNewTab()
                            ->color('primary'),
                        TextEntry::make('notes')
                            ->columnSpanFull(),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
