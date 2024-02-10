<?php

namespace App\Filament\Filament\Resources\SeriesMapResource\Pages;

use App\Filament\Filament\Resources\SeriesMapResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeriesMap extends EditRecord
{
    protected static string $resource = SeriesMapResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
