<?php

namespace App\Filament\Filament\Resources\SeriesResource\Pages;

use App\Filament\Filament\Resources\SeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSeries extends ViewRecord
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
