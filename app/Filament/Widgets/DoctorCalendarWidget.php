<?php

namespace App\Filament\Widgets;

use App\Models\DoctorAvailability;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;
use Illuminate\Support\Carbon;

class DoctorCalendarWidget extends FullCalendarWidget
{
    public ?int $doctorId = null;

    protected static ?string $heading = 'Agenda do Médico';

    public function mount(?int $doctorId = null)
    {
        $this->doctorId = $doctorId;
    }

    public function fetchEvents(array $fetchInfo): array
    {
        if (!$this->doctorId) {
            return [];
        }

        $start = Carbon::parse($fetchInfo['start']);
        $end = Carbon::parse($fetchInfo['end']);

        return DoctorAvailability::query()
            ->with(['doctor', 'serviceable'])
            ->where('doctor_id', $this->doctorId)
            ->whereBetween('available_date', [$start->format('Y-m-d'), $end->format('Y-m-d')])
            ->get()
            ->map(function (DoctorAvailability $availability) {
                $date = Carbon::parse($availability->available_date);
                $start_time = Carbon::parse($availability->start_time)->format('H:i:s');
                $end_time = Carbon::parse($availability->end_time)->format('H:i:s');

                $start_date = Carbon::parse($date->toDateString() . ' ' . $start_time);
                $end_date = Carbon::parse($date->toDateString() . ' ' . $end_time);

                return [
                    'id' => $availability->id,
                    'title' => ($availability->serviceable_type === 'App\Models\Exam' ? 'Exame: ' : 'Especialidade: ') .
                        $availability->serviceable->name,
                    'start' => $start_date->toDateTimeString(),
                    'end' => $end_date->toDateTimeString(),
                ];
            })
            ->toArray();
    }

    protected function getViewOptions(): array
    {
        return [
            'initialView' => 'timeGridWeek',
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay',
            ],
            'slotMinTime' => '06:00:00',
            'slotMaxTime' => '22:00:00',
            'locale' => 'pt-br',
            'buttonText' => [
                'today' => 'Hoje',
                'month' => 'Mês',
                'week' => 'Semana',
                'day' => 'Dia',
            ],
            'allDaySlot' => false,
            'slotDuration' => '00:30:00',
            'weekends' => true,
            'height' => '600px',
            'nowIndicator' => true,
            'eventDisplay' => 'block',
            'displayEventTime' => true,
            'displayEventEnd' => true,
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => false,
                'hour12' => false
            ],
        ];
    }
}
