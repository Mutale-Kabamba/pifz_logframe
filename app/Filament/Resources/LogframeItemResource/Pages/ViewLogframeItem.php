<?php

declare(strict_types=1);

namespace App\Filament\Resources\LogframeItemResource\Pages;

use App\Filament\Resources\LogframeItemResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewLogframeItem extends ViewRecord
{
    protected static string $resource = LogframeItemResource::class;

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
                Section::make('Logframe Item Details')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('project.name')
                            ->label('Project'),
                        TextEntry::make('category')
                            ->badge()
                            ->formatStateUsing(fn ($state): string => $state->label())
                            ->color(fn ($state): string => $state->color()),
                        TextEntry::make('description')
                            ->label('Goal / Description')
                            ->columnSpanFull(),
                        TextEntry::make('indicator')
                            ->label('Indicator'),
                        TextEntry::make('target_value')
                            ->label('Target Value'),
                        TextEntry::make('means_of_verification')
                            ->label('Means of Verification'),
                        TextEntry::make('assumptions'),
                        TextEntry::make('parent.description')
                            ->label('Parent Item')
                            ->placeholder('None'),
                        TextEntry::make('sort_order')
                            ->label('Sort Order'),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ]),
            ]);
    }
}
