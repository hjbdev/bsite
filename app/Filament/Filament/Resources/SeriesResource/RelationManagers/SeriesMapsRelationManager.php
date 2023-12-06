<?php

namespace App\Filament\Filament\Resources\SeriesResource\RelationManagers;

use App\Enums\SeriesMapStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesMapsRelationManager extends RelationManager
{
    protected static string $relationship = 'seriesMaps';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('map_id')
                    ->relationship('map', 'title')
                    ->required(),
                Forms\Components\Select::make('status')
                    ->options([
                        SeriesMapStatus::UPCOMING->value => 'Upcoming',
                        SeriesMapStatus::ONGOING->value => 'Ongoing',
                        SeriesMapStatus::FINISHED->value => 'Finished',
                        SeriesMapStatus::CANCELLED->value => 'Cancelled',
                    ])
                    ->required()
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('map.title')
            ->columns([
                Tables\Columns\TextColumn::make('map.title'),
                Tables\Columns\TextColumn::make('team_a_score')
                    ->numeric(),
                Tables\Columns\TextColumn::make('team_b_score')
                    ->numeric(),
                Tables\Columns\TextColumn::make('rounds_played')
                    ->numeric(),
                Tables\Columns\TextColumn::make('status'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
