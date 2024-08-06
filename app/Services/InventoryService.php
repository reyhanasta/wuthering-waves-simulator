<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Weapon;

class InventoryService
{
    public function getInventory($sessionId)
    {
        $key = 'inventory_' . $sessionId;
        return Redis::hgetall($key);
    }

    public function addToInventory($gachaResult, $sessionId)
    {
        $key = 'inventory_' . $sessionId;
        $itemKey = 'item_' . $gachaResult->id;

        $exists = Redis::hexists($key, $itemKey);

        // Using pipeline to reduce the number of Redis connections
        Redis::pipeline(function ($pipe) use ($key, $itemKey, $exists) {
            if ($exists) {
                $pipe->hincrby($key, $itemKey, 1);
            } else {
                $pipe->hset($key, $itemKey, 1);
            }
        });

        return $exists ? 'yes' : 'no';
    }

    public function refreshInventory($sessionId)
    {
        $key = 'inventory_' . $sessionId;
        $inventory = Redis::hgetall($key);

        if (empty($inventory)) {
            return [];
        }

        $itemIds = array_map(fn ($key) => intval(str_replace('item_', '', $key)), array_keys($inventory));

        $items = Weapon::whereIn('id', $itemIds)->get()->keyBy('id');

        return collect($inventory)->map(function ($count, $key) use ($items) {
            $id = intval(str_replace('item_', '', $key));
            $item = $items[$id] ?? null;
            if ($item) {
                $item->count = $count;
                return $item;
            }
        })->filter()->values();
    }
}
