<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ConsultationChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Consultas nos Últimos 30 Dias';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = collect(range(29, 0))->map(function ($daysAgo) {
            $date = Carbon::now()->subDays($daysAgo)->format('Y-m-d');

            return [
                'date' => Carbon::now()->subDays($daysAgo)->format('d/m'),
                'Consultas' => Consultation::query()
                    ->whereHas('doctorAvailability', function ($query) use ($date) {
                        $query->whereDate('available_date', $date);
                    })
                    ->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'label' => 'Consultas',
                    'data' => $data->pluck('Consultas')->toArray(),
                    'fill' => true,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor' => 'rgb(59, 130, 246)',
                ],
            ],
            'labels' => $data->pluck('date')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
