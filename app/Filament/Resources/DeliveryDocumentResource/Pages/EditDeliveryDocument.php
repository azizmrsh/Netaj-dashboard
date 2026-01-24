<?php

namespace App\Filament\Resources\DeliveryDocumentResource\Pages;

use App\Filament\Resources\DeliveryDocumentResource;
use App\Models\Transporter;
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

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction(),
            Actions\Action::make('saveAndPrint')
                ->label('Save and Print')
                ->action(function () {
                    $this->save();
                    return redirect()->route('delivery-documents.print', ['deliveryDocument' => $this->record->id]);
                })
                ->color('success'),
            $this->getCancelFormAction(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Create or find transporter based on driver information
        if (!empty($data['transport_company_id']) && !empty($data['driver_name'])) {
            $transporter = Transporter::firstOrCreate(
                [
                    'transport_company_id' => $data['transport_company_id'],
                    'name' => $data['driver_name'],
                ],
                [
                    'phone' => $data['driver_phone'] ?? null,
                    'car_no' => $data['car_no'] ?? null,
                    'id_number' => $data['driver_id_number'] ?? null,
                    'is_active' => true,
                ]
            );

            // Update transporter info if it exists but data changed
            if ($transporter->wasRecentlyCreated === false) {
                $transporter->update([
                    'phone' => $data['driver_phone'] ?? $transporter->phone,
                    'car_no' => $data['car_no'] ?? $transporter->car_no,
                    'id_number' => $data['driver_id_number'] ?? $transporter->id_number,
                ]);
            }

            $data['id_transporter'] = $transporter->id;
        }

        return $data;
    }
}
