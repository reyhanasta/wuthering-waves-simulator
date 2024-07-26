<?php

namespace App\Livewire;

use App\Models\Weapon;
use Livewire\Component;
use Illuminate\Support\Facades\Redis;

class Inventory extends Component
{
    public $inventoryItems;

    public function mount($sessionId)
    {
        $this->inventoryItems = $this->fetchInventoryItems($sessionId);
    }

    private function fetchInventoryItems($sessionId)
    {
        $inventory = Redis::hgetall('inventory_' . $sessionId);
        $itemIds = array_keys($inventory);
        dd($itemIds);
        $items = Weapon::whereIn('id', $itemIds)->get();

        foreach ($items as $item) {
            $item->count = $inventory['item_' . $item->id] ?? 0;
        }

        return $items;
    }

    public function render()
    {
        return view('livewire.inventory', [
            'inventoryItems' => $this->inventoryItems,
        ]);
    }
}
