<?php

namespace App\Filament\Resources\CharacterAttributeResource\Pages;

use App\Filament\Resources\CharacterAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCharacterAttribute extends EditRecord
{
    protected static string $resource = CharacterAttributeResource::class;

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
