<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Location;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Consultas Hoje', Consultation::query()
                ->whereHas('doctorAvailability', function ($query) {
                    $query->whereDate('available_date', today());
                })
                ->count())
                ->description('Total de consultas agendadas para hoje')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('Consultas Agendadas', Consultation::where('status', 'scheduled')->count())
                ->description('Total de consultas futuras')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('info'),

            Stat::make('Médicos Ativos', Doctor::count())
                ->description('Total de médicos cadastrados')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),

            Stat::make('Pacientes', Patient::count())
                ->description('Total de pacientes cadastrados')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Locais de Atendimento', Location::count())
                ->description('Total de locais disponíveis')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('gray'),

            Stat::make('Taxa de Conclusão', function () {
                $total = Consultation::whereNot('status', 'scheduled')->count();
                $completed = Consultation::where('status', 'completed')->count();

                if ($total === 0) return '0%';

                return round(($completed / $total) * 100) . '%';
            })
                ->description('Percentual de consultas concluídas')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make('Taxa de Cancelamento', function () {
                $total = Consultation::whereNot('status', 'scheduled')->count();
                $canceled = Consultation::where('status', 'canceled')->count();

                if ($total === 0) return '0%';

                return round(($canceled / $total) * 100) . '%';
            })
                ->description('Percentual de consultas canceladas')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),

            Stat::make('Consultas Este Mês', Consultation::query()
                ->whereHas('doctorAvailability', function ($query) {
                    $query->whereMonth('available_date', now()->month)
                        ->whereYear('available_date', now()->year);
                })
                ->count())
                ->description('Total de consultas no mês atual')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),
        ];
    }
}
