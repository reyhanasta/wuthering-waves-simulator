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
}
