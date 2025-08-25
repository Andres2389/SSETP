<?php

namespace App\Filament\Resources\EtapaProductivaResource\Pages;

use App\Filament\Resources\EtapaProductivaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEtapaProductiva extends CreateRecord
{
    protected static string $resource = EtapaProductivaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}