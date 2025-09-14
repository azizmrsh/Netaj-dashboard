<?php

namespace App\Filament\Resources\DeliveryDocumentResource\Pages;

use App\Filament\Resources\DeliveryDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryDocument extends EditRecord
{
    protected static string $resource = DeliveryDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
