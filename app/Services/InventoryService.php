<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Weapon;

class InventoryService
{
    protected $sessionId;

    public function __construct($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    public function getInventory()
    {
        $key = 'inventory_' . $this->sessionId;
        return Redis::hgetall($key) ?? [];
    }

    public function addToInventory($gachaResult)
    {
        $key = 'inventory_' . $this->sessionId;
        $itemKey = 'item_' . $gachaResult->id;

        if (Redis::hexists($key, $itemKey)) {
            Redis::hincrby($key, $itemKey, 1);
        } else {
            Redis::hset($key, $itemKey, 1);
        }
    }

    public function refreshInventory()
    {
        return $this->fetchInventoryItems();
    }

    private function fetchInventoryItems()
    {
        $key = 'inventory_' . $this->sessionId;
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
