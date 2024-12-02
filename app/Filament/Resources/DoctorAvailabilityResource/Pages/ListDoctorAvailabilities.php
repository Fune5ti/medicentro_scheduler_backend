<?php

namespace App\Filament\Resources\DoctorAvailabilityResource\Pages;

use App\Filament\Resources\DoctorAvailabilityResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Widgets\DoctorAvailabilityWidget;

class ListDoctorAvailabilities extends ListRecords
{
    protected static string $resource = DoctorAvailabilityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DoctorAvailabilityWidget::class
        ];
    }
}
