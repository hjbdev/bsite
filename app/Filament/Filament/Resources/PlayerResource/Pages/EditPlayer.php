<?php

namespace App\Filament\Filament\Resources\PlayerResource\Pages;

use App\Filament\Filament\Resources\PlayerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPlayer extends EditRecord
{
    protected static string $resource = PlayerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
