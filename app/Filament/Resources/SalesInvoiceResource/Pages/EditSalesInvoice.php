<?php

namespace App\Filament\Resources\SalesInvoiceResource\Pages;

use App\Filament\Resources\SalesInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSalesInvoice extends EditRecord
{
    protected static string $resource = SalesInvoiceResource::class;

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
                    return redirect()->route('sales-invoices.print', ['salesInvoice' => $this->record->id]);
                })
                ->color('success'),
            $this->getCancelFormAction(),
        ];
    }
}
