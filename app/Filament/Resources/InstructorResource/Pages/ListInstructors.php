<?php

namespace App\Filament\Resources\InstructorResource\Pages;

use App\Filament\Resources\InstructorResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListInstructors extends ListRecords
{
    protected static string $resource = InstructorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->icon('heroicon-o-plus'),
            \EightyNine\ExcelImport\ExcelImportAction::make()
                ->slideOver()
                ->color("primary")
                ->label('Importar Instructores')
                  ->visible(fn () =>
                    auth()->user()->can('importar instructores') || auth()->user()->hasRole('admin')
                )
                ->after(function () {
                    Notification::make()
                        ->title('Importación exitosa')
                        ->body('Los instructores se han importado correctamente.')
                        ->icon('heroicon-o-check-circle')
                        ->success()
                        ->send();
                }),
        ];
    }
}
