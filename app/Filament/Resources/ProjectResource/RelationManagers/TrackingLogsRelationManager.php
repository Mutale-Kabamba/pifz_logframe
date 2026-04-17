<?php

declare(strict_types=1);

namespace App\Filament\Resources\ProjectResource\RelationManagers;

use App\Enums\LogframeCategory;
use App\Models\LogframeItem;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrackingLogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logframeItems';

    protected static ?string $title = 'Tracking & Data Entry';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('description')
            ->columns([
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (LogframeCategory $state): string => $state->color())
                    ->formatStateUsing(fn (LogframeCategory $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('indicator')
                    ->limit(40),
                TextColumn::make('target_value')
                    ->label('Target'),
                TextColumn::make('trackingLogs')
                    ->label('Entries')
                    ->formatStateUsing(fn ($record): string => (string) $record->trackingLogs()->count())
                    ->badge(),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->options(LogframeCategory::class),
            ])
            ->headerActions([
                Tables\Actions\Action::make('addTrackingEntry')
                    ->label('Add Tracking Entry')
                    ->icon('heroicon-o-plus-circle')
                    ->form([
                        Select::make('logframe_item_id')
                            ->label('Logframe Item')
                            ->options(function (RelationManager $livewire): array {
                                return $livewire->getOwnerRecord()
                                    ->logframeItems()
                                    ->pluck('description', 'id')
                                    ->toArray();
                            })
                            ->required()
                            ->searchable(),
                        TextInput::make('actual_value')
                            ->label('Actual / Registered Value')
                            ->required(),
                        TextInput::make('evidence_link')
                            ->label('Evidence Link / File URL')
                            ->url()
                            ->helperText('Link to the means of verification document'),
                        Textarea::make('notes')
                            ->rows(3),
                        DateTimePicker::make('recorded_at')
                            ->label('Date Recorded')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $data['recorded_by'] = Auth::id();

                        \App\Models\TrackingLog::create($data);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewLogs')
                    ->label('View Logs')
                    ->icon('heroicon-o-eye')
                    ->modalContent(function ($record) {
                        $logs = $record->trackingLogs()
                            ->with('recorder')
                            ->latest('recorded_at')
                            ->get();

                        return view('filament.modals.tracking-logs', [
                            'logs' => $logs,
                            'item' => $record,
                        ]);
                    })
                    ->modalHeading(fn ($record): string => "Tracking Logs: {$record->description}")
                    ->modalSubmitAction(false),
                Tables\Actions\Action::make('quickLog')
                    ->label('Quick Log')
                    ->icon('heroicon-o-plus')
                    ->form([
                        TextInput::make('actual_value')
                            ->label('Actual / Registered Value')
                            ->required(),
                        TextInput::make('evidence_link')
                            ->label('Evidence Link')
                            ->url(),
                        Textarea::make('notes')
                            ->rows(2),
                        DateTimePicker::make('recorded_at')
                            ->default(now())
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->trackingLogs()->create([
                            ...$data,
                            'recorded_by' => Auth::id(),
                        ]);
                    }),
            ]);
    }
}
