<?php

namespace App\Filament\Resources\SalesInvoiceResource\Pages;

use App\Filament\Resources\SalesInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSalesInvoice extends CreateRecord
{
    protected static string $resource = SalesInvoiceResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            Actions\Action::make('createAndPrint')
                ->label('Save and Print')
                ->action(function () {
                    $this->create();
                    return redirect()->route('sales-invoices.print', ['salesInvoice' => $this->record->id]);
                })
                ->color('success'),
            $this->getCancelFormAction(),
        ];
    }
}
