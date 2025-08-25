<?php

namespace App\Filament\Widgets;

use App\Models\BitacoraUpload;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BitacorasChart extends ChartWidget
{
    protected static ?string $heading = 'Bitácoras por Mes';

    protected static ?int $sort = 2;

    public static function canView(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected function getData(): array
    {
        $data = [];
        $labels = [];

        // Obtener datos de los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $month = $date->format('M Y');
            $labels[] = $month;

            $count = BitacoraUpload::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->count();
            
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Bitácoras Subidas',
                    'data' => $data,
                    'backgroundColor' => '#39a900',
                    'borderColor' => '#2d8000',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}