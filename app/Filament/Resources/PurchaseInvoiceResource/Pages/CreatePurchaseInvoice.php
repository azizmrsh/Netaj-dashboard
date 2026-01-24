<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePurchaseInvoice extends CreateRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Actions\Action::make('createAndPrint')
                ->label('Save and Print')
                ->action(function () {
                    $this->create();
                    return redirect()->route('purchase-invoices.print', ['purchaseInvoice' => $this->record->id]);
                })
                ->color('success'),
            $this->getCancelFormAction(),
        ];
    }
}
