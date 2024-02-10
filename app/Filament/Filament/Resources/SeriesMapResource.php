<?php

namespace App\Filament\Filament\Resources;

use App\Filament\Filament\Resources\SeriesMapResource\Pages;
use App\Filament\Filament\Resources\SeriesMapResource\RelationManagers;
use App\Filament\Filament\Resources\SeriesMapResource\RelationManagers\DemoRelationManager;
use App\Models\SeriesMap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesMapResource extends Resource
{
    protected static ?string $model = SeriesMap::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('team_a_score')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->default(0),
                Forms\Components\TextInput::make('team_b_score')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->default(0),
                Forms\Components\Select::make('series_id')
                    ->relationship('series', 'id')
                    ->disabled()
                    ->required(),
                Forms\Components\Select::make('map_id')
                    ->relationship('map', 'name')
                    ->required(),
                Forms\Components\DateTimePicker::make('start_date'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255)
                    ->default('upcoming'),
                Forms\Components\TextInput::make('rounds_played')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('team_a_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('team_b_score')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('series.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('map.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('rounds_played')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            DemoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSeriesMaps::route('/'),
            'show' => Pages\ShowSeriesMap::route('/{record}'),
            // 'create' => Pages\CreateSeriesMap::route('/create'),
            'edit' => Pages\EditSeriesMap::route('/{record}/edit'),
        ];
    }
}
