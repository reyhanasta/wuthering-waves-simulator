<?php 
namespace App\Livewire;

use Livewire\Component;
use App\Services\GachaService;
use App\Services\CacheService;
use App\Services\InventoryService;

class StandardBanner extends Component
{
    public $cacheDuration = 120;
    public $sessionId;
    public $baseDropRates;
    public $gachaResults = [];
    public $inventoryItems = [];

    protected $gachaService;
    protected $cacheService;
    protected $inventoryService;

    public function mount(GachaService $gachaService, CacheService $cacheService, InventoryService $inventoryService)
    {
        $this->sessionId = session()->getId();
        $this->gachaService = $gachaService;
        $this->cacheService = $cacheService;
        $this->inventoryService = $inventoryService;

        $this->baseDropRates = $this->cacheService->getBaseDropRates();
        $this->inventoryItems = $this->inventoryService->getInventory();
    }

    public function singlePull()
    {
        $gachaResult = $this->gachaService->getGachaResult();
        if ($gachaResult) {
            $this->inventoryService->addToInventory($gachaResult);
            $this->inventoryItems = $this->inventoryService->refreshInventory();
            $this->gachaResults = [$gachaResult];
        } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls()
    {
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->gachaService->getGachaResult();
            if ($gachaResult) {
                $this->inventoryService->addToInventory($gachaResult);
                $results[] = $gachaResult;
            }
        }
        $this->inventoryItems = $this->inventoryService->refreshInventory();
        $this->gachaResults = $results;
    }

    public function render()
    {
        return view('livewire.standard-banner', [
            'gachaResults' => $this->gachaResults,
            'inventoryItems' => $this->inventoryItems,
        ]);
    }





    // TERBARU 
    <?php

namespace App\Livewire;

use Log;
use App\Models\Rarity;
use App\Models\Weapon;
use Livewire\Component;
use Spatie\Image\Image;
use Illuminate\Support\Arr;
use App\Services\CacheService;
use App\Services\InventoryService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class StandardBanner extends Component
{
    public $cacheDuration = 120; // Cache duration in minutes
    public $cachedData;
    
    public $gachaResults = [];
    public $inventory = [];
    public $inventoryItems = [];

    public $displayStyle = 'hidden';
    public $sessionId;
    public $bgImg;
    public $gachaBg;
    public $ownedStatus = "no";
    public $gachaImgBg;

    public $get5starId;
    public $get4starId;

    public $baseDropRates;
    public $weaponColor = 'cyan';

    protected $cacheService;
    protected $inventoryService;
    

    public function mount(CacheService $cacheService, InventoryService $inventoryService)
    {
        $this->sessionId = Session::getId();
        $this->cacheService = $cacheService;
        $this->inventoryService = $inventoryService;
        
        $this->baseDropRates = $this->cacheService->getBaseDropRates($this->cacheDuration);
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->gachaBg = Storage::url('public/images/background/T_LuckdrawShare.png');
        // $this->gachaImgBg = Storage::url('public/images/background/T_LuckdrawShare.jpg');
        $this->cachedData = $this->cacheService->getCacheData($this->sessionId);
        $this->inventoryItems = $this->inventoryService->getInventory($this->sessionId);
        $x = $this->inventoryService->refreshInventory($this->sessionId);
        dd($this->inventoryIems);
    }

    public function singlePull(CacheService $cacheService,InventoryService $inventoryService)
    {
        $gachaResult = $this->getGachaResult();
        $this->cacheService = $cacheService;
        $this->inventoryService = $inventoryService;
        Redis::incr('totalPulls_count_' . $this->sessionId);
        if ($gachaResult) {
            $this->bgImg = '';
            $this->displayStyle = 'grid-cols-1';
            $this->ownedStatus= $this->inventoryService->addToInventory($gachaResult,$this->sessionId,$this->ownedStatus);
            $this->gachaResults = [$this->formatGachaResult($gachaResult)];
            $this->cachedData = $this->cacheService->getCacheData($this->sessionId);
            $this->inventoryService->refreshInventory($this->sessionId);
        } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls(CacheService $cacheService,InventoryService $inventoryService)
    {
        $this->cacheService = $cacheService;
        $this->inventoryService = $inventoryService;
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult();
            $this->weaponColor = $this->colorPick($gachaResult->rarity);
            if ($gachaResult) {
                $this->bgImg = '';
                $this->displayStyle = 'grid-cols-5';
                $this->ownedStatus= $this->inventoryService->addToInventory($gachaResult,$this->sessionId,$this->ownedStatus);
                $results[] = $this->formatGachaResult($gachaResult);
                $this->inventoryService->refreshInventory($this->sessionId);
            }
        }
        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->cachedData = $this->cacheService->getCacheData($this->sessionId);
        $this->gachaResults = $results;
    }

    private function getGachaResult()
    {
        $rand = mt_rand(0, 10000) / 100;
        $fourstarpity = Redis::incr('pity4_count_' . $this->sessionId);
        $fivestarpity = Redis::incr('pity5_count_' . $this->sessionId);

        $fiveStarDropRates = $this->baseDropRates->firstWhere('level', 'SSR')->drop_rates;
        $this->get5starId = $this->baseDropRates->firstWhere('level', 'SSR')->id;
        $this->get4starId = $this->baseDropRates->firstWhere('level', 'SR')->id;

        $increasedDropRate = ($fivestarpity >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) :  $fiveStarDropRates;

        if ($fivestarpity == 80) {
            $this->resetPity($this->get5starId);
            return $this->getRandomWeaponByRarity($this->get5starId);
        }

        if ($fourstarpity >= 10 && $fivestarpity < 80) {
            $this->resetPity($this->get4starId);
            return $this->getRandomWeaponByRarity($this->get4starId);
        }

        $cumulativeProbability = 0;
        foreach ($this->baseDropRates as $rates) {
            $cumulativeProbability += ($rates->level == 'SSR') ? $increasedDropRate : $rates->drop_rates;
            if ($rand <= $cumulativeProbability) {
                $this->resetPity($rates->id);
                return $this->getRandomWeaponByRarity($rates->id);
            }
        }
        return null;
    }

    private function getRandomWeaponByRarity($rarity)
    {
        return Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        })->random();
        

    }
    // private function getCacheData()
    // {
    //     return [
    //         'totalPulls' => Redis::get('totalPulls_count_' . $this->sessionId) ?? 0,
    //         'pity4' => Redis::get('pity4_count_' . $this->sessionId) ?? 0,
    //         'pity5' => Redis::get('pity5_count_' . $this->sessionId) ?? 0,
    //         'inventory' => Redis::get('inventory_' . $this->sessionId) ?? []
    //     ];
    // }

    private function formatGachaResult($gachaResult)
    {
        return [
            'id' => $gachaResult->id,
            'name' => $gachaResult->name,
            'img' => $gachaResult->getFirstMediaUrl('weapon','thumb'),
            'type' => $gachaResult->type,
            'rarity' => $gachaResult->rarity,
            'color' => $this->colorPick($gachaResult->rarity),
            'stars' => $this->weaponStars($gachaResult->rarity),
            'owned' => $this->ownedStatus,
        ];
    }
    public function resetPity($rarity)
    {
        if ($rarity == $this->get5starId) {
            Redis::setex('pity5_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        } elseif ($rarity == $this->get4starId) {
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        }
    }

    public function resetAllRecords(CacheService $cacheService,InventoryService $inventoryService)
    {
        $this->cacheService = $cacheService;
        $this->inventoryService = $inventoryService;
        $inventoryKey = 'inventory_' . $this->sessionId;
        Redis::setex('pity5_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        Redis::setex('totalPulls_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        // Redis::hdel('inventory_' . $this->sessionId, 'dummy');
        // Redis::hset('inventory_' . $this->sessionId, 'dummy', 'dummy');
        // Redis::expire('inventory_' . $this->sessionId, $this->cacheDuration * 60);
        // Get all fields in the hash
        $fields = Redis::hkeys($inventoryKey);
        // Delete all fields
        if (!empty($fields)) {
            Redis::hdel($inventoryKey, ...$fields);
        }
        Cache::flush();
        $this->cachedData = $this->cacheService->getCacheData($this->sessionId);
        $this->inventoryService->refreshInventory($this->sessionId);

    }

    public function colorPick($rarity)
    {
        return match ($rarity) {
            $this->get5starId => 'bg-yellow-400',
            $this->get4starId => 'bg-purple-500',
            default => 'bg-slate-800',
        };
    }

    public function weaponStars($rarity)
    {
        return match ($rarity) {
            $this->get5starId => 5,
            $this->get4starId => 4,
            default => 3,
        };
    }

    public function render()
    {
        return view('livewire.standard-banner');
    }

    // private function addToInventory($gachaResult)
    // {

    //     $key = 'inventory_' . $this->sessionId;
    //     $itemKey = 'item_' . $gachaResult->id;

    //     if (Redis::hexists($key, $itemKey)) {
    //         $this->ownedStatus = 'yes';
    //         Redis::hincrby($key, $itemKey, 1);
    //     } else {
    //         $this->ownedStatus = 'no';
    //         Redis::hset($key, $itemKey, 1);
    //     }
    // }

    // private function getInventory()
    // {
    //     $key = 'inventory_' . $this->sessionId;
    //     $inventory = Redis::hgetall($key);

    //     if (!$inventory) {
    //         return [];
    //     }

    //     return $inventory;
    // }

    public function refreshInventory()
    {
        $this->inventoryItems = $this->fetchInventoryItems();
    }

    // private function fetchInventoryItems()
    // {
    //     $key = 'inventory_' . $this->sessionId;
    //     $inventory = Redis::hgetall($key);

    //     if (!$inventory) {
    //         return [];
    //     }

    //     $itemIds = array_map(fn ($key) => intval(str_replace('item_', '', $key)), array_keys($inventory));

    //     $items = Weapon::whereIn('id', $itemIds)->get();
    //     foreach ($items as $item) {
    //         $item->count = $inventory['item_' . $item->id] ?? 0;
    //     }
      
    //     return $items;
    // }
}

}
