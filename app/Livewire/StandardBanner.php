<?php

namespace App\Livewire;

use Log;
use App\Models\Rarity;
use App\Models\Weapon;
use Livewire\Component;
use Spatie\Image\Image;
use Illuminate\Support\Arr;
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
    public $ownedStatus = "no";
    public $gachaImgBg;

    public $get5starId;
    public $get4starId;

    public $baseDropRates;
    public $weaponColor = 'cyan';
    

    public function mount()
    {
        $this->sessionId = Session::getId();
        $this->baseDropRates = Cache::remember('baseDropRates', $this->cacheDuration * 60, function () {
            return Rarity::all();
        });
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        // $this->gachaImgBg = Storage::url('public/images/background/T_LuckdrawShare.jpg');
        $this->cachedData = $this->getCacheData();
        $this->inventory = $this->getInventory();
        $this->refreshInventory();
    }

    public function singlePull()
    {
        $gachaResult = $this->getGachaResult();
        Redis::incr('totalPulls_count_' . $this->sessionId);
        $this->cachedData = $this->getCacheData();
        if ($gachaResult) {
            $this->bgImg = '';
            $this->displayStyle = 'grid-cols-1';
            $this->addToInventory($gachaResult);
            $this->gachaResults = [$this->formatGachaResult($gachaResult)];
            $this->refreshInventory();
        } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls()
    {
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult();
            $this->weaponColor = $this->colorPick($gachaResult->rarity);
            if ($gachaResult) {
                $this->bgImg = '';
                $this->displayStyle = 'grid-cols-5';
                $this->addToInventory($gachaResult);
                $results[] = $this->formatGachaResult($gachaResult);
                $this->refreshInventory();
            }
        }
        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->cachedData = $this->getCacheData();
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
    private function getCacheData()
    {
        return [
            'totalPulls' => Redis::get('totalPulls_count_' . $this->sessionId) ?? 0,
            'pity4' => Redis::get('pity4_count_' . $this->sessionId) ?? 0,
            'pity5' => Redis::get('pity5_count_' . $this->sessionId) ?? 0,
            'inventory' => Redis::get('inventory_' . $this->sessionId) ?? []
        ];
    }

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

    public function resetAllRecords()
    {
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
        $this->cachedData = $this->getCacheData();
        $this->refreshInventory();
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

    private function addToInventory($gachaResult)
    {

        $key = 'inventory_' . $this->sessionId;
        $itemKey = 'item_' . $gachaResult->id;

        if (Redis::hexists($key, $itemKey)) {
            $this->ownedStatus = 'yes';
            Redis::hincrby($key, $itemKey, 1);
        } else {
            $this->ownedStatus = 'no';
            Redis::hset($key, $itemKey, 1);
        }
    }

    private function getInventory()
    {
        $key = 'inventory_' . $this->sessionId;
        $inventory = Redis::hgetall($key);

        if (!$inventory) {
            return [];
        }

        return $inventory;
    }

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

        $itemIds = array_map(fn ($key) => intval(str_replace('item_', '', $key)), array_keys($inventory));

        $items = Weapon::whereIn('id', $itemIds)->get();

        foreach ($items as $item) {
            $item->count = $inventory['item_' . $item->id] ?? 0;
        }

        return $items;
    }
}
