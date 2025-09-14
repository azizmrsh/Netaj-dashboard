<?php

namespace App\Filament\Resources\ReceiptDocumentResource\Pages;

use App\Filament\Resources\ReceiptDocumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceiptDocument extends EditRecord
{
    protected static string $resource = ReceiptDocumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
