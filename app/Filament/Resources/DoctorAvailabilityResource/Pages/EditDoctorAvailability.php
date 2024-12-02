<?php

namespace App\Filament\Resources\DoctorAvailabilityResource\Pages;

use App\Filament\Resources\DoctorAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDoctorAvailability extends EditRecord
{
    protected static string $resource = DoctorAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
