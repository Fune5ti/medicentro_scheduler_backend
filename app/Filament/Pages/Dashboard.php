<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\ConsultationChartWidget;
use App\Filament\Widgets\ConsultationStatusChartWidget;
use App\Filament\Widgets\DashboardStatsOverview;
use App\Filament\Widgets\DoctorAvailabilityWidget;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    public function getTitle(): string
    {
        return 'Painel de Controle';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DashboardStatsOverview::class,
        ];
    }

    public function getWidgets(): array
    {
        return [
            ConsultationStatusChartWidget::class,
            ConsultationChartWidget::class,
            DoctorAvailabilityWidget::class
        ];
    }
}
