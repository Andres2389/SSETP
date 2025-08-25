<?php

namespace App\Filament\Widgets;

use App\Models\EtapaProductiva;
use App\Models\Ficha;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EtapaProductivaOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = EtapaProductiva::query();

        // Aplicar filtros según el rol del usuario
        if (auth()->user()?->isInstructor()) {
            $query->whereHas('instructor', function ($q) {
                $q->whereHas('users', function ($userQuery) {
                    $userQuery->where('id', auth()->id());
                });
            });
        } elseif (auth()->user()?->isAprendiz()) {
            $query->where('numero_documento', auth()->user()->numero_documento);
        }

        $totalAprendices = $query->count();
        $certificados = (clone $query)->where('estado_sofia', 'CERTIFICADO')->count();
        $enFormacion = (clone $query)->where('estado_sofia', 'EN FORMACIÓN')->count();

        $stats = [
            Stat::make('Total Aprendices', $totalAprendices)
                ->description('En etapa productiva')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make('Certificados', $certificados)
                ->description('Aprendices certificados')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make('En Formación', $enFormacion)
                ->description('Aprendices activos')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];

        // Solo mostrar esta estadística si no es aprendiz
        if (!auth()->user()?->isAprendiz()) {
            $stats[] = Stat::make('Fichas Activas', Ficha::where('estado_ficha', 'ACTIVA')->count())
                ->description('Total de fichas')
                ->descriptionIcon('heroicon-m-folder-open')
                ->color('info');
        }

        return $stats;
    }
}
