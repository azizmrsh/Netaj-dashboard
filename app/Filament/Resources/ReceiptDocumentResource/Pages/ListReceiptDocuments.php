<?php

namespace App\Filament\Resources\ReceiptDocumentResource\Pages;

use App\Filament\Resources\ReceiptDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReceiptDocuments extends ListRecords
{
    protected static string $resource = ReceiptDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
