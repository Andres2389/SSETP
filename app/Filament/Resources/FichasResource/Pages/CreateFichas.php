<?php

namespace App\Filament\Resources\FichasResource\Pages;

use App\Filament\Resources\FichasResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFichas extends CreateRecord
{
    protected static string $resource = FichasResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
