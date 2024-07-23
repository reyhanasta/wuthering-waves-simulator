<?php

namespace App\Filament\Resources\RarityResource\Pages;

use App\Filament\Resources\RarityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRarity extends EditRecord
{
    protected static string $resource = RarityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
