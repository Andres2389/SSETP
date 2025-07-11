<?php

namespace App\Filament\Resources\InstructoresResource\Pages;

use App\Filament\Resources\InstructoresResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInstructores extends EditRecord
{
    protected static string $resource = InstructoresResource::class;

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
