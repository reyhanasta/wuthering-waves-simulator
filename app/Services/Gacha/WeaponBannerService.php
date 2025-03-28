<?php

namespace App\Services\Gacha;

use App\Models\Weapon;
use App\Services\InventoryService;

class WeaponBannerService
{
    protected InventoryService $inventoryService;
    protected string $sessionId;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
        $this->sessionId = session()->getId();
    }

    public function pull(int $count = 1): array
    {
        $results = [];

        for ($i = 0; $i < $count; $i++) {
            $result = $this->getRandomWeapon();
            $this->inventoryService->addToInventory($result, $this->sessionId);
            $results[] = $result;
        }

        return $results;
    }

    private function getRandomWeapon(): Weapon
    {
        $rarity = $this->determineRarity();
        return Weapon::where('rarity_id', $rarity)->inRandomOrder()->first();
    }

    private function determineRarity(): int
    {
        $chance = rand(1, 1000);
        return match (true) {
            $chance <= 15 => 1,   // 1.5%
            $chance <= 110 => 2,  // 9.5%
            default => 3,         // 89%
        };
    }
}
