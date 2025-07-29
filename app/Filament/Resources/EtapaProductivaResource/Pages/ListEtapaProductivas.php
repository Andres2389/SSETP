<?php

namespace App\Filament\Resources\EtapaProductivaResource\Pages;

use App\Filament\Resources\EtapaProductivaResource;
use Filament\Actions;
use App\Imports\MyEtapaProductivaImport;
use Filament\Resources\Pages\ListRecords;


class ListEtapaProductivas extends ListRecords
{
    protected static string $resource = EtapaProductivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            \EightyNine\ExcelImport\ExcelImportAction::make()->use(MyEtapaProductivaImport::class)->visible(fn () => !auth()->user()->can('ver etapaproductiva') || auth()->user()->hasRole('admin')),

        ];
    }
    


}
