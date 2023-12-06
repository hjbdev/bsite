<?php

namespace App\Filament\Filament\Resources\OrganiserResource\Pages;

use App\Filament\Filament\Resources\OrganiserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganisers extends ListRecords
{
    protected static string $resource = OrganiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
