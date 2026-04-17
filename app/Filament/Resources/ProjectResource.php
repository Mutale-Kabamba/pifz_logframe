<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\LogframeCategory;
use App\Filament\Resources\ProjectResource\Pages;
use App\Filament\Resources\ProjectResource\RelationManagers;
use App\Models\Project;
use App\Models\User;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-briefcase';

    protected static string|\UnitEnum|null $navigationGroup = 'Project Management';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Project')
                    ->tabs([
                        Tab::make('Project Details')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('General Information')
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Select::make('assigned_officer_id')
                                            ->label('Assigned Officer')
                                            ->relationship('assignedOfficer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        DatePicker::make('start_date')
                                            ->required(),
                                        DatePicker::make('end_date')
                                            ->required()
                                            ->afterOrEqual('start_date'),
                                        Textarea::make('description')
                                            ->columnSpanFull()
                                            ->rows(3),
                                        TextInput::make('spreadsheet_id')
                                            ->label('Google Spreadsheet ID')
                                            ->helperText('The ID from the Google Sheets URL')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Logframe Setup')
                            ->icon('heroicon-o-table-cells')
                            ->schema([
                                Section::make('Impact')
                                    ->description('The long-term, broad change the project contributes to.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('impacts')
                                            ->relationship()
                                            ->label('')
                                            ->defaultItems(0)
                                            ->schema(static::logframeFieldSchema())
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['category'] = LogframeCategory::Impact->value;

                                                return $data;
                                            })
                                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderableWithButtons(),
                                    ]),

                                Section::make('Outcomes')
                                    ->description('The medium-term results. Track % of startups securing funding, number of students developing business ideas.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('outcomes')
                                            ->relationship()
                                            ->label('')
                                            ->defaultItems(0)
                                            ->schema(static::logframeFieldSchema())
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['category'] = LogframeCategory::Outcome->value;

                                                return $data;
                                            })
                                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderableWithButtons(),
                                    ]),

                                Section::make('Outputs')
                                    ->description('The direct deliverables. Attendance Sheets, Training Evaluations, School Activity Reports.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('outputs')
                                            ->relationship()
                                            ->label('')
                                            ->defaultItems(0)
                                            ->schema(static::logframeFieldSchema())
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['category'] = LogframeCategory::Output->value;

                                                return $data;
                                            })
                                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderableWithButtons(),
                                    ]),

                                Section::make('Activities')
                                    ->description('The actions performed. Masterclasses, Digital Skills Clubs, Hackathons, Financial Literacy Workshops.')
                                    ->collapsible()
                                    ->schema([
                                        Repeater::make('activities')
                                            ->relationship()
                                            ->label('')
                                            ->defaultItems(0)
                                            ->schema(static::logframeFieldSchema())
                                            ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                                                $data['category'] = LogframeCategory::Activity->value;

                                                return $data;
                                            })
                                            ->itemLabel(fn (array $state): ?string => $state['description'] ?? null)
                                            ->collapsible()
                                            ->cloneable()
                                            ->reorderableWithButtons(),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    /** @return array<int, Component> */
    protected static function logframeFieldSchema(): array
    {
        return [
            TextInput::make('description')
                ->label('Goal / Description')
                ->required()
                ->columnSpanFull(),
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
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('assignedOfficer.name')
                    ->label('Officer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('logframe_items_count')
                    ->label('Logframe Items')
                    ->counts('logframeItems')
                    ->sortable(),
                TextColumn::make('spreadsheet_id')
                    ->label('Sheets')
                    ->formatStateUsing(fn (?string $state): string => $state ? 'Connected' : 'Not set')
                    ->badge()
                    ->color(fn (?string $state): string => $state ? 'success' : 'gray'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('assigned_officer_id')
                    ->label('Officer')
                    ->relationship('assignedOfficer', 'name'),
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

    public static function getRelations(): array
    {
        return [
            RelationManagers\TrackingLogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
            'view' => Pages\ViewProject::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        /** @var User $user */
        $user = Auth::user();

        if ($user->hasRole('project_officer')) {
            $query->where('assigned_officer_id', $user->id);
        }

        return $query;
    }
}
