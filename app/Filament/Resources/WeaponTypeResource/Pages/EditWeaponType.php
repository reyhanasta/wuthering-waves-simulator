<?php

namespace App\Filament\Resources\WeaponTypeResource\Pages;

use App\Filament\Resources\WeaponTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWeaponType extends EditRecord
{
    protected static string $resource = WeaponTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
