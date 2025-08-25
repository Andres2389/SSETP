<?php

namespace App\Filament\Resources\EtapaProductivaResource\Pages;

use App\Filament\Resources\EtapaProductivaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewEtapaProductiva extends ViewRecord
{
    protected static string $resource = EtapaProductivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}