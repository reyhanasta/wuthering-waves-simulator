<?php

namespace App\Filament\Resources\RarityResource\Pages;

use App\Filament\Resources\RarityResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateRarity extends CreateRecord
{
    protected static string $resource = RarityResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
