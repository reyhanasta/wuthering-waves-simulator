<?php

namespace App\Livewire;

use Log;
use App\Models\Rarity;
use App\Models\Weapon;
use Livewire\Component;
use Spatie\Image\Image;
use Illuminate\Support\Arr;
use App\Services\CacheService;
use App\Services\GachaService;
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


    protected $inventoryService;


    public function mount(CacheService $cacheService,InventoryService $inventoryService)
    {
        $this->sessionId = Session::getId();
        $this->baseDropRates = $this->getBaseDropRates($this->cacheDuration);

        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->gachaBg = Storage::url('public/images/background/T_LuckdrawShare.png');
        // $this->gachaImgBg = Storage::url('public/images/background/T_LuckdrawShare.jpg');
        $this->cachedData = $cacheService->getCacheData($this->sessionId);

        $this->inventory = $inventoryService->getInventory($this->sessionId);
        $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);

    }

    public function getBaseDropRates($cacheDuration)
    {
        return Cache::remember('baseDropRates',$cacheDuration * 60, function () {
            return Rarity::all();
        });
    }

    public function singlePull(CacheService $cacheService,InventoryService $inventoryService,GachaService $gachaService){
        $gachaResult = $gachaService->getGachaResult($this->baseDropRates,$this->cacheDuration);
        Redis::incr('totalPulls_count_' . $this->sessionId);
        if ($gachaResult) {
            $this->bgImg = '';
            $this->displayStyle = 'grid-cols-1';
            $this->ownedStatus = $inventoryService->addToInventory($gachaResult,$this->sessionId);
            $this->gachaResults = [$this->formatGachaResult($gachaResult)];
            $this->cachedData = $cacheService->getCacheData($this->sessionId);
            $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);
        } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls(CacheService $cacheService,InventoryService $inventoryService,GachaService $gachaService){
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $gachaService->getGachaResult($this->baseDropRates,$this->cacheDuration);
            $this->weaponColor = $this->colorPick($gachaResult->rarity);
            if ($gachaResult) {
                $this->bgImg = '';
                $this->displayStyle = 'grid-cols-5';
                $this->ownedStatus = $inventoryService->addToInventory($gachaResult,$this->sessionId);
                $results[] = $this->formatGachaResult($gachaResult);
                $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);
            }
        }
        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->cachedData = $cacheService->getCacheData($this->sessionId);
        $this->gachaResults = $results;
    }

    // private function getGachaResult($gachaService){
    //     $rand = mt_rand(0, 10000) / 100;
    //     $fourstarpity = Redis::incr('pity4_count_' . $this->sessionId);
    //     $fivestarpity = Redis::incr('pity5_count_' . $this->sessionId);

    //     $fiveStarDropRates = $this->baseDropRates->firstWhere('level', 'SSR')->drop_rates;
    //     $this->get5starId = $this->baseDropRates->firstWhere('level', 'SSR')->id;
    //     $this->get4starId = $this->baseDropRates->firstWhere('level', 'SR')->id;

    //     $increasedDropRate = ($fivestarpity >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) :  $fiveStarDropRates;

    //     if ($fivestarpity == 80) {
    //         $this->resetPity($this->get5starId);
    //         return $gachaService->getRandomWeaponByRarity($this->get5starId,$this->cacheDuration);
    //     }

    //     if ($fourstarpity >= 10 && $fivestarpity < 80) {
    //         $this->resetPity($this->get4starId);
    //         return $gachaService->getRandomWeaponByRarity($this->get4starId,$this->cacheDuration);
    //     }

    //     $cumulativeProbability = 0;
    //     foreach ($this->baseDropRates as $rates) {
    //         $cumulativeProbability += ($rates->level == 'SSR') ? $increasedDropRate : $rates->drop_rates;
    //         if ($rand <= $cumulativeProbability) {
    //             $this->resetPity($rates->id);
    //             return $gachaService->getRandomWeaponByRarity($rates->id,$this->cacheDuration);
    //         }
    //     }
    //     return null;
    // }

    // private function getRandomWeaponByRarity($rarity){
    //     return Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
    //         return Weapon::where('rarity', $rarity)->get();
    //     })->random();
    // }

    private function formatGachaResult($gachaResult){
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
    public function resetPity($rarity){
        if ($rarity == $this->get5starId) {
            Redis::setex('pity5_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        } elseif ($rarity == $this->get4starId) {
            Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        }
    }

    public function resetAllRecords(CacheService $cacheService,InventoryService $inventoryService){
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
        $this->cachedData = $cacheService->getCacheData($this->sessionId);
        $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);
    }

    public function colorPick($rarity){
        return match ($rarity) {
            $this->get5starId => 'bg-yellow-400',
            $this->get4starId => 'bg-purple-500',
            default => 'bg-slate-800',
        };
    }

    public function weaponStars($rarity){
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

}
