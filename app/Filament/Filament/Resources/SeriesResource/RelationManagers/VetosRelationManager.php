<?php

namespace App\Filament\Filament\Resources\SeriesResource\RelationManagers;

use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;

class VetosRelationManager extends RelationManager
{
    protected static string $relationship = 'vetos';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('team_id')
                    ->relationship(
                        name: 'team',
                        titleAttribute: 'name',
                        modifyQueryUsing: function (Builder $query) {
                            $requestUri = request()->headers->get('referer');
                            $seriesId = str($requestUri)->after('series/')->before('/edit')->__toString();
                            $series = Series::findOrFail($seriesId);
                            $query->whereIn('id', [$series->team_a_id, $series->team_b_id]);
                        }
                    )
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('type')
                    ->options([
                        'ban' => 'Ban',
                        'pick' => 'Pick',
                        'left-over' => 'Left Over'
                    ])
                    ->required(),
                Forms\Components\Select::make('map_id')
                    ->relationship(
                        name: 'map', #
                        titleAttribute: 'title',
                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('title')
                    )
                    ->preload()
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('type')
            ->columns([
                Tables\Columns\TextColumn::make('type'),
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
