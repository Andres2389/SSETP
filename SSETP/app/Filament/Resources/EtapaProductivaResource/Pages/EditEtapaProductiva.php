<?php

namespace App\Filament\Resources\EtapaProductivaResource\Pages;

use App\Filament\Resources\EtapaProductivaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEtapaProductiva extends EditRecord
{
    protected static string $resource = EtapaProductivaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}