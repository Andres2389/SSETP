<?php

namespace App\Filament\Resources\FichasResource\Pages;

use App\Filament\Resources\FichasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFichas extends EditRecord
{
    protected static string $resource = FichasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
