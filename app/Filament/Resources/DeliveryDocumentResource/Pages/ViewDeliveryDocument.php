<?php

namespace App\Filament\Resources\DeliveryDocumentResource\Pages;

use App\Filament\Resources\DeliveryDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewDeliveryDocument extends ViewRecord
{
    protected static string $resource = DeliveryDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}