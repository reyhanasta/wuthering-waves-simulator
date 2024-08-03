<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Weapon;

class InventoryService
{
    public function getInventory($sessionId)
    {
        $key = 'inventory_' . $sessionId;
        return Redis::hgetall($key) ?? [];

    }

    public function addToInventory($gachaResult,$sessionId)
    {
        $key = 'inventory_' . $sessionId;
        $itemKey = 'item_' . $gachaResult->id;

        if (Redis::hexists($key, $itemKey)) {
            Redis::hincrby($key, $itemKey, 1);
            return 'yes';
        } else {
            Redis::hset($key, $itemKey, 1);
            return 'no';
        }
    }

    public function refreshInventory($sessionId)
    {
        return $this->fetchInventoryItems($sessionId);
    }

    private function fetchInventoryItems($sessionId)
    {
        $key = 'inventory_' . $sessionId;
        $inventory = Redis::hgetall($key);

        if (!$inventory) {
            return [];
        }

        $itemIds = array_map(fn ($key) => intval(str_replace('item_', '', $key)), array_keys($inventory));

        $items = Weapon::whereIn('id', $itemIds)->get();

        foreach ($items as $item) {
            $item->count = $inventory['item_' . $item->id] ?? 0;
        }

        return $items;
    }
}
