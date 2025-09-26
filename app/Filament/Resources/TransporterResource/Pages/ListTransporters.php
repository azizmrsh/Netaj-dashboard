<?php

namespace App\Filament\Resources\TransporterResource\Pages;

use App\Filament\Resources\TransporterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTransporters extends ListRecords
{
    protected static string $resource = TransporterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return TransporterResource::getWidgets();
    }
}
