<?php

namespace App\Filament\Resources\DoctorAvailabilityResource\Pages;

use App\Filament\Resources\DoctorAvailabilityResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Notifications\Notification;

class CreateDoctorAvailability extends CreateRecord
{
    protected static string $resource = DoctorAvailabilityResource::class;

    protected function beforeCreate(): void
    {
        // Validate that end date is after or equal to start date
        $startDate = Carbon::parse($this->data['date_range_from']);
        $endDate = Carbon::parse($this->data['date_range_to']);

        if ($endDate->isBefore($startDate)) {
            Notification::make()
                ->title('Data final deve ser maior que a data inicial')
                ->danger()
                ->send();

            $this->halt();
        }
    }

    protected function afterCreate(): void
    {
        // Get the date range
        $startDate = Carbon::parse($this->data['date_range_from']);
        $endDate = Carbon::parse($this->data['date_range_to']);

        // Create a period between the dates
        $period = CarbonPeriod::create($startDate, $endDate);

        // Get selected weekdays
        $weekdays = collect($this->data['weekdays'])->map(fn($day) => (int) $day);

        // Create additional records
        foreach ($period as $date) {
            // Skip the first date as it was already created by the default process
            if ($date->format('Y-m-d') === $startDate->format('Y-m-d')) {
                continue;
            }

            // Check if the current day is selected
            if ($weekdays->contains($date->dayOfWeek)) {
                $availability = new ($this->getModel())([
                    'doctor_id' => $this->data['doctor_id'],
                    'available_date' => $date->format('Y-m-d'),
                    'start_time' => $this->data['start_time'],
                    'end_time' => $this->data['end_time'],
                    'serviceable_type' => $this->data['serviceable_type'],
                    'serviceable_id' => $this->data['serviceable_id'],
                ]);

                $availability->save();
            }
        }

        Notification::make()
            ->title('Disponibilidades criadas com sucesso')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set the available_date to the first date in the range
        $data['available_date'] = $data['date_range_from'];

        return $data;
    }
}
