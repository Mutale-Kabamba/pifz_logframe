<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LogframeCategory;
use App\Filament\Resources\TrackingLogResource\Pages;
use App\Models\LogframeItem;
use App\Models\TrackingLog;
use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TrackingLogResource extends Resource
{
    protected static ?string $model = TrackingLog::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static string | \UnitEnum | null $navigationGroup = 'Project Management';

    protected static ?string $navigationLabel = 'Tracking Logs';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        /** @var User $user */
        $user = Auth::user();

        return $schema
            ->components([
                Section::make('Tracking Entry')
                    ->columns(2)
                    ->schema([
                        Select::make('logframe_item_id')
                            ->label('Logframe Item')
                            ->options(function () use ($user): array {
                                $query = LogframeItem::query()
                                    ->with('project');

                                if ($user->hasRole('project_officer')) {
                                    $query->whereHas('project', fn (Builder $q) => $q->where('assigned_officer_id', $user->id));
                                }

                                return $query->get()
                                    ->mapWithKeys(fn (LogframeItem $item): array => [
                                        $item->id => "[{$item->project->name}] {$item->category->label()}: {$item->description}",
                                    ])
                                    ->toArray();
                            })
                            ->searchable()
                            ->required()
                            ->columnSpanFull(),
                        TextInput::make('actual_value')
                            ->label('Actual / Registered Value')
                            ->required(),
                        DateTimePicker::make('recorded_at')
                            ->label('Date Recorded')
                            ->default(now())
                            ->required(),
                        TextInput::make('evidence_link')
                            ->label('Evidence Link / Verification URL')
                            ->url()
                            ->helperText('Link to the means of verification (attendance sheets, reports, etc.)')
                            ->columnSpanFull(),
                        Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('logframeItem.project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('logframeItem.category')
                    ->label('Category')
                    ->badge()
                    ->color(fn (LogframeCategory $state): string => $state->color())
                    ->formatStateUsing(fn (LogframeCategory $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('logframeItem.description')
                    ->label('Item')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('actual_value')
                    ->label('Value')
                    ->weight('bold'),
                TextColumn::make('recorder.name')
                    ->label('Recorded By')
                    ->sortable(),
                TextColumn::make('recorded_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('evidence_link')
                    ->label('Evidence')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'View' : '-')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->color('primary'),
            ])
            ->defaultSort('recorded_at', 'desc')
            ->filters([
                SelectFilter::make('category')
                    ->label('Logframe Category')
                    ->options(LogframeCategory::class)
                    ->query(function (Builder $query, array $data): Builder {
                        if (filled($data['value'])) {
                            $query->whereHas('logframeItem', fn (Builder $q) => $q->where('category', $data['value']));
                        }

                        return $query;
                    }),
                SelectFilter::make('project')
                    ->label('Project')
                    ->relationship('logframeItem.project', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrackingLogs::route('/'),
            'create' => Pages\CreateTrackingLog::route('/create'),
            'edit' => Pages\EditTrackingLog::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('project_officer')) {
            $query->whereHas(
                'logframeItem.project',
                fn (Builder $q) => $q->where('assigned_officer_id', $user->id),
            );
        }

        return $query;
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['recorded_by'] = Auth::id();

        return $data;
    }
}
