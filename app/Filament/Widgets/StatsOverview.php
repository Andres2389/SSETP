<?php

namespace App\Filament\Widgets;

use App\Models\BitacoraUpload;
use App\Models\EtapaProductiva;
use App\Models\Ficha;
use App\Models\Instructor;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->getAdminStats();
        } elseif ($user->isInstructor()) {
            return $this->getInstructorStats();
        } elseif ($user->isAprendiz()) {
            return $this->getAprendizStats();
        }

        return [];
    }

    private function getAdminStats(): array
    {
        return [
            Stat::make('Total Fichas', Ficha::count())
                ->description('Fichas registradas')
                ->descriptionIcon('heroicon-o-academic-cap')
                ->color('primary'),

            Stat::make('Total Instructores', Instructor::count())
                ->description('Instructores activos')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('success'),

            Stat::make('Aprendices en EP', EtapaProductiva::count())
                ->description('En etapa productiva')
                ->descriptionIcon('heroicon-o-users')
                ->color('warning'),

            Stat::make('Bitácoras Pendientes', BitacoraUpload::where('estado_revision', 'pendiente')->count())
                ->description('Por revisar')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
        ];
    }

    private function getInstructorStats(): array
    {
        $instructorId = auth()->user()->instructor_id;
        
        $aprendicesAsignados = EtapaProductiva::where('instructores_id', $instructorId)->count();
        
        $bitacorasPendientes = BitacoraUpload::whereHas('etapaProductiva', function ($query) use ($instructorId) {
            $query->where('instructores_id', $instructorId);
        })->where('estado_revision', 'pendiente')->count();

        $bitacorasRevisadas = BitacoraUpload::whereHas('etapaProductiva', function ($query) use ($instructorId) {
            $query->where('instructores_id', $instructorId);
        })->where('reviewed_by', auth()->id())->count();

        return [
            Stat::make('Aprendices Asignados', $aprendicesAsignados)
                ->description('Bajo su supervisión')
                ->descriptionIcon('heroicon-o-users')
                ->color('primary'),

            Stat::make('Bitácoras Pendientes', $bitacorasPendientes)
                ->description('Por revisar')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Bitácoras Revisadas', $bitacorasRevisadas)
                ->description('Este mes')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),
        ];
    }

    private function getAprendizStats(): array
    {
        $etapaProductivaId = auth()->user()->etapa_productiva_id;
        
        if (!$etapaProductivaId) {
            return [];
        }

        $bitacorasSubidas = BitacoraUpload::where('etapa_productiva_id', $etapaProductivaId)->count();
        $bitacorasAceptadas = BitacoraUpload::where('etapa_productiva_id', $etapaProductivaId)
            ->where('estado_revision', 'aceptado')->count();
        $bitacorasPendientes = BitacoraUpload::where('etapa_productiva_id', $etapaProductivaId)
            ->where('estado_revision', 'pendiente')->count();
        $bitacorasDevueltas = BitacoraUpload::where('etapa_productiva_id', $etapaProductivaId)
            ->where('estado_revision', 'devuelto')->count();

        return [
            Stat::make('Bitácoras Subidas', $bitacorasSubidas)
                ->description('Total subidas')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary'),

            Stat::make('Bitácoras Aceptadas', $bitacorasAceptadas)
                ->description('Aprobadas')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Bitácoras Pendientes', $bitacorasPendientes)
                ->description('En revisión')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Bitácoras Devueltas', $bitacorasDevueltas)
                ->description('Para corregir')
                ->descriptionIcon('heroicon-o-x-circle')
                ->color('danger'),
        ];
    }
}