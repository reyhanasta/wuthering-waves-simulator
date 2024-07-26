<?php

namespace App\Livewire;

use App\Models\Rarity;
use App\Models\Weapon;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class StandardBanner extends Component
{

    public $cacheDuration = 120; // Cache duration in minutes
    public $cachedData;
    public $resetCache;
    public $gachaResults = [];
    public $inventory = [];

    public $displayStyle = 'hidden';
    public $sessionId;
    public $bgImg;
    public $gachaImgBg;

    public $get5starId;
    public $get4starId;

    public $baseDropRates;
    public $weaponColor = 'cyan';

    public function mount()
    {
        $this->sessionId = Session::getId();
        $this->baseDropRates = Rarity::all();
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->gachaImgBg = Storage::url('public/images/background/T_LuckdrawShare.jpg');
        $this->cachedData = $this->getCacheData($this->sessionId);
        $this->inventory = $this->getInventory($this->sessionId);
    }

    public function singlePull()
    {
        $this->sessionId = Session::getId();
        $this->initializeCache($this->sessionId);
        $gachaResult = $this->getGachaResult($this->sessionId);
        Redis::incr('totalPulls_count_' . $this->sessionId);
        $this->cachedData = $this->getCacheData($this->sessionId);

        if ($gachaResult) {
            $this->bgImg = '';
            $this->displayStyle = 'grid-cols-1';
            $this->gachaResults = [$this->formatGachaResult($gachaResult)];
            $this->addToInventory($gachaResult, $this->sessionId);
        } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls()
    {
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult($this->sessionId);
            $this->weaponColor = $this->colorPick($gachaResult->rarity);
            if ($gachaResult) {
                $this->bgImg = '';
                $this->displayStyle = 'grid-cols-5';
                $results[] = $this->formatGachaResult($gachaResult);
                $this->addToInventory($gachaResult, $this->sessionId);
            }
        }
        Redis::incrby('totalPulls_count_' . $this->sessionId, 10);
        $this->cachedData = $this->getCacheData($this->sessionId);
        $this->gachaResults = $results;
    }

    private function getGachaResult($sessionId)
    {
        // Logika untuk mendapatkan hasil gacha
        $rand = mt_rand(0, 10000) / 100;
        $fourstarpity = Redis::incr('pity4_count_' . $sessionId);
        $fivestarpity = Redis::incr('pity5_count_' . $sessionId);
        //Ambil data drop rate
        $fiveStarDropRates = Rarity::where('level', 'SSR')->value('drop_rates');
        $this->get5starId = Rarity::where('level', 'SSR')->value('id');
        $this->get4starId = Rarity::where('level', 'SR')->value('id');
        //Soft pity
        $increasedDropRate = ($fivestarpity >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) :  $fiveStarDropRates;

        //4-Star guaranteed per 10 pulls
        if ($fourstarpity >= 10 && $fivestarpity < 80) {
            $this->resetpity($this->get4starId, $sessionId);
            return $this->getRandomWeaponByRarity($this->get4starId);
        }
        //5-Star guaranteed per 80 pulls
        if ($fivestarpity == 80) {
            $this->resetpity($this->get5starId, $sessionId);
            return $this->getRandomWeaponByRarity($this->get5starId);
        }

        $cumulativeProbability = 0;
        foreach ($this->baseDropRates as $rates) {
            //Soft pity check
            $cumulativeProbability += ($rates->level == 'SSR') ? $rates->drop_rates * $increasedDropRate : $rates->drop_rates;
            if ($rand <= $cumulativeProbability) {
                $this->resetpity($rates->id, $sessionId);
                return $this->getRandomWeaponByRarity($rates->id);
            }
        }
        return null;
    }

    // public function setpityDefault($sessionId)
    // {
    //     return [
    //         'totalPulls' => Redis::set('totalPulls_count_' . $sessionId,0) ?? 0,
    //         'pity4' => Redis::set('pity4_count_' . $sessionId,0) ?? 0,
    //         'pity5' => Redis::set('pity5_count_' . $sessionId,0) ?? 0
    //     ];
    // }

    private function initializeCache($sessionId)
    {
        $this->cacheWithDefault('totalPulls_count_' . $sessionId, 0);
        $this->cacheWithDefault('pity4_count_' . $sessionId, 0);
        $this->cacheWithDefault('pity5_count_' . $sessionId, 0);
        $this->cacheWithDefault('inventory_' . $sessionId, []);
    }

    private function cacheWithDefault($key, $default)
    {
        if (!Redis::exists($key)) {
            Redis::setex($key, $this->cacheDuration * 60, $default);
        }
    }

    private function getCacheData($sessionId)
    {
        return [
            'totalPulls' => Redis::get('totalPulls_count_' . $sessionId) ?? 0,
            'pity4' => Redis::get('pity4_count_' . $sessionId) ?? 0,
            'pity5' => Redis::get('pity5_count_' . $sessionId) ?? 0,
            'inventory' => Redis::get('inventory_' . $sessionId) ?? 0
        ];
    }

    private function formatGachaResult($gachaResult)
    {
        return [
            'id' => $gachaResult->id,
            'name' => $gachaResult->name,
            'img' => Storage::url($gachaResult->img),
            'type' => $gachaResult->type,
            'rarity' => $gachaResult->rarity,
            'color' => $this->colorPick($gachaResult->rarity),
            'stars' => $this->weaponStars($gachaResult->rarity),
        ];
    }

    private function getRandomWeaponByRarity($rarity)
    {
        return Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        })->random();
    }

    public function resetPity($rarity, $sessionId)
    {
        if ($rarity == $this->get5starId) {
            Redis::setex('pity5_count_' . $sessionId, $this->cacheDuration * 60, 0);
            Redis::setex('pity4_count_' . $sessionId, $this->cacheDuration * 60, 0);
        } elseif ($rarity == $this->get4starId) {
            Redis::setex('pity4_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
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

    //MODUL INVENTORY
    private function addToInventory($gachaResult, $sessionId)
    {
        $inventory = $this->getInventory($sessionId);
        $inventory[] = $gachaResult->id; // Store the weapon ID or any necessary data
        Redis::setex('inventory_' . $sessionId, $this->cacheDuration * 60, json_encode($inventory));
    }

    private function getInventory($sessionId)
    {
        $inventory = Redis::get('inventory_' . $sessionId);
        return $inventory ? json_decode($inventory, true) : [];
    }

    // private function fetchInventoryItems()
    // {
    //     return Weapon::whereIn('id', $this->inventory)->get();
    // }
}
