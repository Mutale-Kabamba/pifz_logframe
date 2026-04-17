<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Enums\LogframeCategory;
use App\Filament\Resources\ProjectResource;
use App\Models\Project;
use Filament\Actions;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project Summary')
                    ->icon('heroicon-o-information-circle')
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('assignedOfficer.name')
                            ->label('Assigned Officer'),
                        TextEntry::make('spreadsheet_id')
                            ->label('Google Sheets')
                            ->formatStateUsing(fn (?string $state): string => $state ? 'Connected' : 'Not configured')
                            ->badge()
                            ->color(fn (?string $state): string => $state ? 'success' : 'gray'),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->date(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ]),

                Tabs::make('Logframe Matrix')
                    ->columnSpanFull()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Impact')
                            ->icon('heroicon-o-star')
                            ->schema([
                                RepeatableEntry::make('impacts')
                                    ->label('')
                                    ->schema(static::logframeInfolistSchema()),
                            ]),
                        Tab::make('Outcomes')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                RepeatableEntry::make('outcomes')
                                    ->label('')
                                    ->schema(static::logframeInfolistSchema()),
                            ]),
                        Tab::make('Outputs')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                RepeatableEntry::make('outputs')
                                    ->label('')
                                    ->schema(static::logframeInfolistSchema()),
                            ]),
                        Tab::make('Activities')
                            ->icon('heroicon-o-bolt')
                            ->schema([
                                RepeatableEntry::make('activities')
                                    ->label('')
                                    ->schema(static::logframeInfolistSchema()),
                            ]),
                    ]),
            ]);
    }

    /** @return array<int, \Filament\Infolists\Components\Entry> */
    protected static function logframeInfolistSchema(): array
    {
        return [
            TextEntry::make('description')
                ->label('Goal / Description')
                ->columnSpanFull(),
            TextEntry::make('indicator')
                ->label('Indicator'),
            TextEntry::make('target_value')
                ->label('Target'),
            TextEntry::make('means_of_verification')
                ->label('Means of Verification'),
            TextEntry::make('assumptions'),
        ];
    }
}
