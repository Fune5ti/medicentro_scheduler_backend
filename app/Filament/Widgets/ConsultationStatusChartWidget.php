<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Filament\Widgets\ChartWidget;

class ConsultationStatusChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribuição de Status das Consultas';

    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $statuses = [
            'scheduled' => 'Agendada',
            'completed' => 'Concluída',
            'canceled' => 'Cancelada',
        ];

        $data = collect($statuses)->map(function ($label, $status) {
            return [
                'label' => $label,
                'value' => Consultation::where('status', $status)->count(),
            ];
        });

        return [
            'datasets' => [
                [
                    'data' => $data->pluck('value')->toArray(),
                    'backgroundColor' => [
                        'rgb(59, 130, 246)', // blue-500
                        'rgb(34, 197, 94)', // green-500
                        'rgb(239, 68, 68)', // red-500
                    ],
                ],
            ],
            'labels' => $data->pluck('label')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
