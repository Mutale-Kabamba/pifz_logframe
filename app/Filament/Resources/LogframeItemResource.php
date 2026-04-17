<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LogframeCategory;
use App\Filament\Resources\LogframeItemResource\Pages;
use App\Models\LogframeItem;
use App\Models\User;
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

class LogframeItemResource extends Resource
{
    protected static ?string $model = LogframeItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-table-cells';

    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';

    protected static ?string $navigationLabel = 'Logframe Items';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Logframe Item Details')
                    ->columns(2)
                    ->schema([
                        Select::make('project_id')
                            ->label('Project')
                            ->relationship('project', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('category')
                            ->options(LogframeCategory::class)
                            ->required(),
                        Select::make('parent_id')
                            ->label('Parent Item')
                            ->relationship(
                                'parent',
                                'description',
                                fn (Builder $query, ?LogframeItem $record) => $query
                                    ->when($record, fn (Builder $q) => $q->where('id', '!=', $record->id))
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        TextInput::make('sort_order')
                            ->numeric()
                            ->default(0)
                            ->minValue(0),
                        Textarea::make('description')
                            ->label('Goal / Description')
                            ->required()
                            ->columnSpanFull()
                            ->rows(3),
                        TextInput::make('indicator')
                            ->label('Indicator / Metric')
                            ->helperText('e.g., "% of startups securing funding"')
                            ->columnSpanFull(),
                        TextInput::make('target_value')
                            ->label('Target Value'),
                        TextInput::make('means_of_verification')
                            ->label('Means of Verification')
                            ->helperText('e.g., "Attendance sheets", "Workshop reports"'),
                        Textarea::make('assumptions')
                            ->helperText('e.g., "Industry experts are available"')
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('category')
                    ->badge()
                    ->color(fn (LogframeCategory $state): string => $state->color())
                    ->formatStateUsing(fn (LogframeCategory $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('indicator')
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('target_value')
                    ->label('Target')
                    ->toggleable(),
                TextColumn::make('tracking_logs_count')
                    ->label('Entries')
                    ->counts('trackingLogs')
                    ->sortable()
                    ->badge(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('project_id')
            ->filters([
                SelectFilter::make('project_id')
                    ->label('Project')
                    ->relationship('project', 'name'),
                SelectFilter::make('category')
                    ->options(LogframeCategory::class),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLogframeItems::route('/'),
            'create' => Pages\CreateLogframeItem::route('/create'),
            'edit' => Pages\EditLogframeItem::route('/{record}/edit'),
            'view' => Pages\ViewLogframeItem::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('project_officer')) {
            $query->whereHas(
                'project',
                fn (Builder $q) => $q->where('assigned_officer_id', $user->id),
            );
        }

        return $query;
    }
}
