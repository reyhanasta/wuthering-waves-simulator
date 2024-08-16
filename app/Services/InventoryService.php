<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;
use App\Models\Weapon;
use App\Models\Character;
use Illuminate\Database\Eloquent\Model;

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
        $itemKey = $this->getItemKey($gachaResult);

        $exists = Redis::hexists($key, $itemKey);

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

        $weaponIds = [];
        $characterIds = [];

        foreach (array_keys($inventory) as $key) {
            list($type, $id) = explode('_', $key);
            if ($type === 'weapon') {
                $weaponIds[] = intval($id);
            } elseif ($type === 'character') {
                $characterIds[] = intval($id);
            }
        }

        $weapons = Weapon::whereIn('id', $weaponIds)->get()->keyBy('id');
        $characters = Character::whereIn('id', $characterIds)->get()->keyBy('id');

        return collect($inventory)->map(function ($count, $key) use ($weapons, $characters) {
            list($type, $id) = explode('_', $key);
            $id = intval($id);
            $item = $type === 'weapon' ? ($weapons[$id] ?? null) : ($characters[$id] ?? null);
            if ($item) {
                $item->count = $count;
                return $item;
            }
        })->filter()->values();
    }

    private function getItemKey(Model $item): string
    {
        $type = $item instanceof Weapon ? 'weapon' : 'character';
        return "{$type}_{$item->id}";
    }
}
