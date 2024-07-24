<?php

namespace App\Filament\Resources\WeaponTypeResource\Pages;

use App\Filament\Resources\WeaponTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWeaponType extends CreateRecord
{
    protected static string $resource = WeaponTypeResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
