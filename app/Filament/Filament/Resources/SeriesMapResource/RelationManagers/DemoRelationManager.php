<?php

namespace App\Filament\Filament\Resources\SeriesMapResource\RelationManagers;

use App\Models\Demo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DemoRelationManager extends RelationManager
{
    protected static string $relationship = 'demo';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('path')
                    ->label('Demo')
                    ->maxSize(1024 * 700) // 700mb
                    ->disk('local')
                    ->directory('demo-tmp')
                    ->visibility('private')
                    ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('path')
            ->columns([
                Tables\Columns\TextColumn::make('path'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('size'),
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
