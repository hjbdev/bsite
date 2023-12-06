<?php

namespace App\Filament\Filament\Resources\OrganiserResource\Pages;

use App\Filament\Filament\Resources\OrganiserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganiser extends ViewRecord
{
    protected static string $resource = OrganiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
