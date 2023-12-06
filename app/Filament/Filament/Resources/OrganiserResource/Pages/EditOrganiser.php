<?php

namespace App\Filament\Filament\Resources\OrganiserResource\Pages;

use App\Filament\Filament\Resources\OrganiserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganiser extends EditRecord
{
    protected static string $resource = OrganiserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
