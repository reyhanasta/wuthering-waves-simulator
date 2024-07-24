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
    public $gachaResults = [];

    public $displayStyle = 'hidden';
    public $sessionId;
    public $bgImg;
    public $gachaImgBg;

    public  $get5starId;
    public $get4starId;

    public $baseDropRates;
    public $weaponColor = 'cyan';

    public function mount()
    {
        $sessionId = Session::getId();
        $this->baseDropRates = Rarity::all();
        $this->bgImg = Storage::url('public/images/background/gacha-banner.jpg');
        $this->gachaImgBg = Storage::url('public/images/background/T_LuckdrawShare.jpg');
        $this->cachedData = $this->getCacheData($sessionId);
    }

    public function singlePull()
    {
        $this->sessionId = Session::getId();
        $this->initializeCache($this->sessionId);
        $gachaResult = $this->getGachaResult($this->sessionId);
        Redis::incr('totalPulls_count_' . $this->sessionId);
        $this->cachedData = $this->getCacheData($this->sessionId);
        if ($gachaResult) {
            $this->bgImg='';
            $this->displayStyle='grid-cols-1';
            $this->gachaResults = [
                [
                'id' => $gachaResult->id,
                'name' => $gachaResult->name,
                'img' => Storage::url($gachaResult->img),
                'type' => $gachaResult->type,
                'rarity' => $gachaResult->rarity,
                'color' =>   $this->colorPick($gachaResult->rarity),
                'stars' =>  $this->weaponStars($gachaResult->rarity),
                ]
            ];
         } else {
            $this->gachaResults = ['errors'];
        }
    }

    public function tenPulls(){
        $results = [];
        for ($i = 0; $i < 10; $i++) {
            $gachaResult = $this->getGachaResult($this->sessionId);
            $this->weaponColor = $this->colorPick($gachaResult->rarity);
            if ($gachaResult) {
                $this->bgImg='';
                $this->displayStyle='grid-cols-5';
                $results[] = [
                    'id' => $gachaResult->id,
                    'name' => $gachaResult->name,
                    'img' => Storage::url($gachaResult->img),
                    'type' => $gachaResult->type,
                    'rarity' => $gachaResult->rarity,
                    'color' =>   $this->colorPick($gachaResult->rarity),
                    'stars' => $this->weaponStars($gachaResult->rarity)
                ];
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
        $fourstarPitty = Redis::incr('pitty4_count_' . $sessionId);
        $fivestarPitty = Redis::incr('pitty5_count_' . $sessionId);
        //Ambil data drop rate
        $fiveStarDropRates = Rarity::where('level','SSR')->value('drop_rates');
        $this->get5starId = Rarity::where('level','SSR')->value('id');
        $this->get4starId = Rarity::where('level','SR')->value('id');
        //Soft pity
        $increasedDropRate = ($fivestarPitty >= 70) ? $fiveStarDropRates * 1.8 + (1 / 100) :  $fiveStarDropRates;

        //4-Star guaranteed per 10 pulls
        if ($fourstarPitty >= 10 && $fivestarPitty < 80) {
            $this->resetPitty($this->get4starId, $sessionId);
            return $this->getRandomWeaponByRarity($this->get4starId);
        }
        //5-Star guaranteed per 80 pulls
        if ($fivestarPitty == 80) {
            $this->resetPitty($this->get5starId, $sessionId);
            return $this->getRandomWeaponByRarity($this->get5starId);
        }

        $cumulativeProbability = 0;
        foreach ($this->baseDropRates as $rates) {
            //Soft pity check
            $cumulativeProbability += ($rates->level == 'SSR') ? $rates->drop_rates * $increasedDropRate : $rates->drop_rates;
            if ($rand <= $cumulativeProbability) {
                $this->resetPitty($rates->id, $sessionId);
                return $this->getRandomWeaponByRarity($rates->id);
            }
        }

        return null;
    }


    private function initializeCache($sessionId)
    {
        if (!Redis::exists('totalPulls_count_' . $sessionId)) {
            Redis::setex('totalPulls_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
        if (!Redis::exists('pitty4_count_' . $sessionId)) {
            Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
        if (!Redis::exists('pitty5_count_' . $sessionId)) {
            Redis::setex('pitty5_count_' . $sessionId, $this->cacheDuration * 60, 0);
        }
    }

    private function getCacheData($sessionId)
    {
        return [
            'totalPulls' => Redis::get('totalPulls_count_' . $sessionId) ?? 0,
            'pitty4' => Redis::get('pitty4_count_' . $sessionId) ?? 0,
            'pitty5' => Redis::get('pitty5_count_' . $sessionId) ?? 0
        ];
    }

    private function getRandomWeaponByRarity($rarity)
    {
        $weapons = Cache::remember("weapons_rarity_{$rarity}", $this->cacheDuration * 60, function () use ($rarity) {
            return Weapon::where('rarity', $rarity)->get();
        });
        return $weapons->random();
    }

    public function resetPitty($rarity, $sessionId)
    {
        switch ($rarity) {
            case $this->get5starId :
                Redis::setex('pitty5_count_' . $sessionId, $this->cacheDuration * 60, 0);
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            case $this->get4starId :
                Redis::setex('pitty4_count_' . $sessionId, $this->cacheDuration * 60, 0);
                break;
            default:
                break;
        }
    }

    public function colorPick($rarity){
            if($rarity == $this->get5starId){
                    return 'bg-yellow-400';
            }else if($rarity == $this->get4starId){
                return 'bg-purple-500';
            }
            return 'bg-slate-800';
    }

    public function weaponStars($rarity){
        if($rarity == $this->get5starId){
            return 5;
        }else if($rarity == $this->get4starId){
            return 4;
        }
        return 3;
    }


    public function render()
    {
        return view('livewire.standard-banner');
    }
}
