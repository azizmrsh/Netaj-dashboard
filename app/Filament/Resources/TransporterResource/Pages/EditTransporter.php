<?php

namespace App\Filament\Resources\TransporterResource\Pages;

use App\Filament\Resources\TransporterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTransporter extends EditRecord
{
    protected static string $resource = TransporterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
