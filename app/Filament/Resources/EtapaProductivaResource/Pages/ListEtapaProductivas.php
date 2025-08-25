<?php

namespace App\Filament\Resources\EtapaProductivaResource\Pages;

use App\Filament\Resources\EtapaProductivaResource;
use Filament\Actions;

use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use App\Imports\EtapaProductivaImport;
use EightyNine\ExcelImport\ExcelImportAction;
use App\Exports\EtapaProductivaExport;
use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;


class ListEtapaProductivas extends ListRecords
{
    protected static string $resource = EtapaProductivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus'),

            ExcelImportAction::make()
                ->use(EtapaProductivaImport::class)
                ->slideOver()
                ->label('Importar Etapas Productivas')
                ->color('primary')
                ->visible(fn () =>
                    auth()->user()->can('importar etapaproductiva') || auth()->user()->hasRole('admin')
                )
                ->after(function () {
                    Notification::make()
                        ->title('Importación exitosa')
                        ->body('Las etapas productivas se han importado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),

            Action::make('export')
                ->label('Exportar datos')
               ->icon('heroicon-m-arrow-down-tray')
                ->visible(fn () =>
                    auth()->user()->can('exportar etapaproductiva') || auth()->user()->hasRole('admin')
                )
                ->action(function () {
                    return Excel::download(new EtapaProductivaExport, 'etapas_productivas.xlsx');
                }),
        ];
    }
}
