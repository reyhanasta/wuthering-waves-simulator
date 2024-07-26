<?php

namespace App\Livewire;

use App\Models\Weapon;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Redis;

class Inventory extends Component
{
    public $inventoryItems;
    public $sessionId;
    
    
    public function mount($sessionId)
    {
        $this->sessionId = $sessionId;
        $this->refreshInventory();
    }

    #[On('inventory-updated')]
    public function refreshInventory()
    {
        $this->inventoryItems = $this->fetchInventoryItems();
    }
    private function fetchInventoryItems()
    {
        $key = 'inventory_' . $this->sessionId;
        $inventory = Redis::hgetall($key);

        if (!$inventory) {
            return [];
        }

        $itemIds = array_map(function($key) {
            return intval(str_replace('item_', '', $key));
        }, array_keys($inventory));

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
