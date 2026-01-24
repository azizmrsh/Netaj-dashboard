<?php

namespace App\Filament\Resources\PurchaseInvoiceResource\Pages;

use App\Filament\Resources\PurchaseInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseInvoice extends EditRecord
{
    protected static string $resource = PurchaseInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Actions\Action::make('saveAndPrint')
                ->label('Save and Print')
                ->action(function () {
                    $this->save();
                    return redirect()->route('purchase-invoices.print', ['purchaseInvoice' => $this->record->id]);
                })
                ->color('success'),
            $this->getCancelFormAction(),
        ];
    }
}
