<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\StatsOverviewWidget as BaseStatsOverviewWidget;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BitacorasChart;
use App\Filament\Widgets\RecentActivity;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Panel de Control - SSETP';

    public function getColumns(): int | array
    {
        return 2;
    }

    public function getWidgets(): array
    {
        $widgets = [];

        if (auth()->user()->isAdmin()) {
            $widgets = [
                StatsOverview::class,
                BitacorasChart::class,
                RecentActivity::class,
            ];
        } elseif (auth()->user()->isInstructor()) {
            $widgets = [
                StatsOverview::class,
                RecentActivity::class,
            ];
        } elseif (auth()->user()->isAprendiz()) {
            $widgets = [
                StatsOverview::class,
            ];
        }

        return $widgets;
    }
}
