<?php

namespace App\Filament\Resources\InstructoresResource\Pages;

use App\Filament\Resources\InstructoresResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstructores extends ListRecords
{
    protected static string $resource = InstructoresResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()->visible(fn () => !auth()->user()->can('ver instructores') || auth()->user()->hasRole('admin')),
        ];
    }
}
