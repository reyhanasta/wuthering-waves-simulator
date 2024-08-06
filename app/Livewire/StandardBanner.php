<?php

namespace App\Livewire;

use Log;
use App\Models\Rarity;
use App\Models\Weapon;
use Livewire\Component;
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
    public $isLoading = false;

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

    public function mount(CacheService $cacheService, InventoryService $inventoryService)
    {
        $this->sessionId = Session::getId();
        $this->baseDropRates = $this->getBaseDropRates($this->cacheDuration);

        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->cachedData = $cacheService->getCacheData($this->sessionId);

        $this->inventory = $inventoryService->getInventory($this->sessionId);
        $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);

        $this->get5starId = $this->baseDropRates->firstWhere('level', 'SSR')->id;
        $this->get4starId = $this->baseDropRates->firstWhere('level', 'SR')->id;
    }

    public function getBaseDropRates($cacheDuration)
    {
        return Cache::remember('baseDropRates', $cacheDuration * 60, function () {
            return Rarity::select('id', 'level')->get();
        });
    }

    public function singlePull(CacheService $cacheService, InventoryService $inventoryService, GachaService $gachaService)
    {
        // Debugging statement
        \Log::info('Starting single pull...');
        $this->dispatch('loading', ['isLoading' => true]);
        $gachaResult = $gachaService->getGachaResult($this->baseDropRates, $this->cacheDuration, $this->sessionId);
        Redis::incr('totalPulls_count_' . $this->sessionId);

        if ($gachaResult) {
            $this->processGachaResults([$gachaResult], $inventoryService);
            $this->cachedData = $cacheService->getCacheData($this->sessionId);
            $this->displayStyle = 'grid-cols-1';
            $this->dispatch('loading', ['isLoading' => false]);
        } else {
            $this->gachaResults = ['errors'];
            $this->dispatch('loading', ['isLoading' => false]);
        }
        \Log::info('Single pull completed.');
    }

    public function tenPulls(CacheService $cacheService, InventoryService $inventoryService, GachaService $gachaService)
    {
        // Debugging statement
        \Log::info('Starting ten pulls...');
        $this->dispatch('loading', ['isLoading' => true]);
        $this->displayStyle = 'grid-cols-5';
        $results = [];

        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $gachaService->getGachaResult($this->baseDropRates, $this->cacheDuration, $this->sessionId);
            if ($gachaResult) {
                $results[] = $gachaResult;
            }
        }

        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->processGachaResults($results, $inventoryService);
        $this->cachedData = $cacheService->getCacheData($this->sessionId);
        $this->dispatch('loading', ['isLoading' => false]);
        \Log::info('Ten pulls completed.');
    }

    private function processGachaResults(array $gachaResults, InventoryService $inventoryService)
    {
        $this->gachaResults = collect($gachaResults)->map(function($gachaResult) use ($inventoryService) {
            return $this->formatGachaResult($gachaResult, $inventoryService);
        })->toArray();

        $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);
    }

    private function formatGachaResult($gachaResult, InventoryService $inventoryService)
    {
        return [
            'id' => $gachaResult->id,
            'name' => $gachaResult->name,
            'img' => $gachaResult->getFirstMediaUrl('weapon', 'thumb'),
            'type' => $gachaResult->type,
            'rarity' => $gachaResult->rarity,
            'color' => $this->colorPick($gachaResult->rarity),
            'stars' => $this->weaponStars($gachaResult->rarity),
            'owned' => $inventoryService->addToInventory($gachaResult, $this->sessionId),
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

    public function resetAllRecords(CacheService $cacheService, InventoryService $inventoryService)
    {
        $inventoryKey = 'inventory_' . $this->sessionId;
        Redis::setex('pity5_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        Redis::setex('pity4_count_' . $this->sessionId, $this->cacheDuration * 60, 0);
        Redis::setex('totalPulls_count_' . $this->sessionId, $this->cacheDuration * 60, 0);

        $fields = Redis::hkeys($inventoryKey);
        if (!empty($fields)) {
            Redis::hdel($inventoryKey, ...$fields);
        }

        Cache::flush();
        $this->cachedData = $cacheService->getCacheData($this->sessionId);
        $this->inventoryItems = $inventoryService->refreshInventory($this->sessionId);
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
}
