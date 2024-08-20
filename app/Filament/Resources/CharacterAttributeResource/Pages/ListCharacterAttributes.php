<?php

namespace App\Filament\Resources\CharacterAttributeResource\Pages;

use App\Filament\Resources\CharacterAttributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCharacterAttributes extends ListRecords
{
    protected static string $resource = CharacterAttributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
