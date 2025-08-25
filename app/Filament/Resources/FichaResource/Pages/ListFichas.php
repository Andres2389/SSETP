<?php

namespace App\Filament\Resources\FichaResource\Pages;

use App\Filament\Resources\FichaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Notifications\Notification;
use Maatwebsite\Excel\Facades\Excel;

class ListFichas extends ListRecords
{
    protected static string $resource = FichaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus'),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->color('primary')
                ->label('Importar Fichas')
                 ->visible(fn () =>
                    auth()->user()->can('importar fichas') || auth()->user()->hasRole('admin')
                )
                 ->after(function () {
                    Notification::make()
                        ->title('Importación exitosa')
                        ->body('Las fichas se han importado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),


        ];
    }
}
