<?php

namespace App\Filament\Resources\DeliveryDocumentResource\Pages;

use App\Filament\Resources\DeliveryDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryDocuments extends ListRecords
{
    protected static string $resource = DeliveryDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return DeliveryDocumentResource::getWidgets();
    }
}
