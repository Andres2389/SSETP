<?php

namespace App\Filament\Resources\EtapaProductivaResource\Widgets;

use App\Models\EtapaProductiva;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget\Card;


class EtapaProductivasOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Card::make('Total Aprendices', EtapaProductiva::count())
            ->color('success')
            ->icon('heroicon-o-users'),


            Card::make('Contratos de Aprendizaje', EtapaProductiva::where('tipo_alternativa', 'contrato de aprendizaje')->count())
            ->icon('heroicon-o-document-text'),

            Card::make('Monitoria', EtapaProductiva::where('tipo_alternativa', 'monitoria')->count())
             ->icon('heroicon-o-document-text'),

             Card::make('Vinculo Laboral', EtapaProductiva::where('tipo_alternativa', 'vinculo laboral')->count())
             ->icon('heroicon-o-document-text'),


            Card::make('Proyecto productivo', EtapaProductiva::where('tipo_alternativa', 'proyecto productivo')->count())
             ->icon('heroicon-o-light-bulb'),

            Card::make('Pasantia', EtapaProductiva::where('tipo_alternativa', 'pasantia')->count())
             ->icon('heroicon-o-hand-raised'),

            // Puedes seguir agregando más tipos si tienes más
        ];
    }
}
