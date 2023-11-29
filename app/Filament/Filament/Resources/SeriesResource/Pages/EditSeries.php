<?php

namespace App\Filament\Filament\Resources\SeriesResource\Pages;

use App\Filament\Filament\Resources\SeriesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeries extends EditRecord
{
    protected static string $resource = SeriesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
