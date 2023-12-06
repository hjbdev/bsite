<?php

namespace App\Filament\Filament\Resources;

use App\Enums\SeriesStatus;
use App\Filament\Filament\Resources\SeriesResource\Pages;
use App\Filament\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->maxLength(255),
                Forms\Components\Select::make('event_id')
                    ->searchable()
                    ->relationship('event', 'name'),
                Forms\Components\Select::make('team_a_id')
                    ->relationship('teamA', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('team_b_id')
                    ->relationship('teamB', 'name')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('current_series_map_id')
                    ->disabled()
                    ->searchable()
                    ->relationship('currentSeriesMap', 'id'),
                Forms\Components\TextInput::make('team_a_score')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('team_b_score')
                    ->required()
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('rounds_played')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->default(0),
                Forms\Components\Select::make('terrorist_team_id')
                    ->searchable()
                    ->disabled()
                    ->relationship('terroristTeam', 'name'),
                Forms\Components\Select::make('ct_team_id')
                    ->searchable()
                    ->disabled()
                    ->relationship('ctTeam', 'name'),
                Forms\Components\Select::make('type')
                    ->options([
                        'bo1' => 'BO1',
                        'bo2' => 'BO2',
                        'bo3' => 'BO3',
                        'bo5' => 'BO5',
                        'bo7' => 'BO7'
                    ])
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        SeriesStatus::CANCELLED->value => 'Cancelled',
                        SeriesStatus::FINISHED->value => 'Finished',
                        SeriesStatus::ONGOING->value => 'Ongoing',
                        SeriesStatus::UPCOMING->value => 'Upcoming'
                    ])
                    ->required()
                    ->default('upcoming'),
                Forms\Components\DateTimePicker::make('start_date'),
                Forms\Components\TextInput::make('stage')
                    ->maxLength(255),
                Forms\Components\TextInput::make('round')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('event.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teamA.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('teamB.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_a_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_b_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('stage')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('round')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
        return [
            RelationManagers\VetosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            // 'view' => Pages\ViewSeries::route('/{record}'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
