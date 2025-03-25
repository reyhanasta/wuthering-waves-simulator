<main class="container px-4 py-8 mx-auto lg:px-60">
    <div class="grid gap-2 lg:gap-12">
        <div id="gachaContainer" class="flex flex-col items-center justify-center space-y-4">
            <div class="w-full max-w-4xl">
                <!-- Banner Area -->
                <div id="bannerArea" wire:loading.attr="disabled" wire:loading.class="opacity-50"
                    x-data="{ isLoading: false }" x-on:loading.window="isLoading = $event.detail.isLoading"
                    class="relative flex flex-col items-center justify-center">
                    <h2>Standard Banner</h2><br>
                    @if ($gachaResults)
                    <!-- Loading Indicator -->
                    <div wire:loading class="absolute z-50 flex items-center justify-center">
                        <div
                            class="w-16 h-16 border-4 border-t-4 border-blue-500 rounded-full border-t-white animate-spin">
                        </div>
                    </div>

                    <div id="gachaResult"
                        class="grid {{ count($gachaResults) === 1 ? 'flex justify-center' : 'grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5' }} gap-2 py-2 px-10 m-2 border-2 shadow-xl rounded-xl {{$displayStyle}} weapon-bg h-auto max-w-full bg-cover bg-center bg-no-repeat">

                        @foreach ($gachaResults as $item)
                        <div
                            class="relative overflow-hidden bg-gray-700 bg-center border-2 border-solid rounded-lg {{ $item['color'] }}">
                            @if ($item['owned'] == 'no')
                            <div class="absolute top-0 left-0 p-1 text-xs font-bold text-white bg-yellow-500">
                                New
                            </div>
                            @endif
                            <div id="weapon" class="relative">
                                <img class="object-cover w-full border-b-2 max-h-56" loading="lazy"
                                    src="{{ $item['img'] }}" alt="{{ $item['name'] }}">
                                <div class="absolute bottom-0 right-0">
                                    <p class="text-xl text-yellow-400">{{ str_repeat('★', $item['stars']) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-20 p-4">
                                <p class="text-center text-white text-md">{{ $item['name'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Default Banner -->
                    <img id="bannerImg" x-show="!isLoading"
                        class="object-cover object-center w-full max-w-4xl mx-auto shadow-lg rounded-2xl"
                        src="{{ $bgImg }}" alt="Banner Image" />
                    @endif
                </div>
            </div>
        </div>
        <!-- Status and Navigation Area -->
        <div class="grid grid-cols-1 gap-4 mt-4 text-white">
            <!-- Pull Buttons -->
            <div class="grid grid-cols-2 gap-4 lg:pb-4">
                <button type="button" wire:click="singlePull" wire:loading.attr="disabled"
                    wire:loading.class="opacity-50" wire:target='singlePull,tenPulls'
                    class="py-3 text-black transition bg-white rounded-md hover:bg-blue-600 hover:text-white">
                    Single Pull
                </button>

                <button type="button" wire:click="tenPulls" wire:loading.attr="disabled" wire:loading.class="opacity-50"
                    wire:target='singlePull,tenPulls'
                    class="py-3 text-black transition bg-white rounded-md hover:bg-blue-600 hover:text-white">
                    10x Pulls
                </button>
            </div>
            <!-- Pull Status -->
            <div class="p-4 bg-gray-800 rounded-lg">
                <div id="pullCounter" class="space-y-2">
                    <h3 class="pb-2 text-lg font-semibold border-b">Summon Stats</h3>
                    <ul class="space-y-1">
                        <li>Total Summons: {{ $cachedData['totalPulls'] ?? 0 }}</li>
                        <li>Summons since last 4★: {{ $cachedData['pity4'] ?? 0 }}</li>
                        <li>Summons since last 5★: {{ $cachedData['pity5'] ?? 0 }}</li>
                    </ul>
                </div>

                <!-- Navigation Buttons -->
                <div id="nav-bar" class="grid grid-cols-3 gap-1 mx-3 mt-3">
                    <button type="button" data-modal-target="inventory-modal" data-modal-toggle="inventory-modal"
                        class="flex items-center justify-center p-2 space-x-2 transition bg-gray-700 rounded hover:bg-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                        </svg>
                        <span class="text-sm">Inventory</span>
                    </button>

                    <button type="button" wire:click="resetAllRecords()" x-on:click="$wire.$refresh()"
                        class="flex items-center justify-center p-2 space-x-2 transition bg-gray-700 rounded hover:bg-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span class="text-sm">Reset</span>
                    </button>

                    <button type="button" data-modal-target="detail-note-modal" data-modal-toggle="detail-note-modal"
                        class="flex items-center justify-center p-2 space-x-2 transition bg-gray-700 rounded hover:bg-blue-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                        </svg>
                        <span class="text-sm">Details</span>
                    </button>
                </div>
            </div>


        </div>
    </div>
    </div>
    </div>

    <!-- Inventory Modal -->
    <div id="inventory-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-x-hidden overflow-y-auto">
        <div class="relative w-full max-w-4xl mx-4 my-12">
            <div class="relative bg-gray-800 border-2 border-yellow-300 shadow-lg rounded-2xl">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-white">Inventory List</h3>
                    <button type="button" data-modal-hide="inventory-modal"
                        class="p-2 text-gray-400 rounded-lg hover:bg-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>
                @if ($inventoryItems)

                <div class="grid grid-cols-1 gap-4 p-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($inventoryItems as $item)
                    <div class="flex items-center p-2 bg-gray-500 border-2 border-yellow-300 rounded-lg">
                        <img src="{{$item->getFirstMediaUrl('gacha','thumb')}}" alt="{{ $item->name }}"
                            class="object-cover w-12 h-12 mr-4 rounded-full">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $item->name }}</p>
                            <p class="text-yellow-500">
                                @if ($item->rarity == 1)
                                {{str_repeat('★',5)}}
                                @elseif($item->rarity == 2)
                                {{str_repeat('★',4)}}
                                @else
                                {{str_repeat('★',3)}}
                                @endif
                            </p>
                            <p class="text-xs text-white">{{ $item->count }}x</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 text-center text-white">
                    No items in inventory
                </div>
                @endif

                <div class="p-4 border-t">
                    <button data-modal-hide="inventory-modal" type="button"
                        class="w-full py-2 text-black bg-white rounded-lg hover:bg-blue-100">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Note Modal -->
    <div id="detail-note-modal" tabindex="-1" aria-hidden="true"
        class="fixed inset-0 z-50 hidden overflow-x-hidden overflow-y-auto">
        <div class="relative w-full max-w-2xl mx-4 my-12">
            <div class="relative bg-gray-800 border-2 border-yellow-300 shadow-lg rounded-2xl">
                <div class="flex items-center justify-between p-4 border-b">
                    <h3 class="text-xl font-semibold text-white">Rate Details</h3>
                    <button type="button" data-modal-hide="detail-note-modal"
                        class="p-2 text-gray-400 rounded-lg hover:bg-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-white">
                    <h4 class="text-lg font-bold underline">Information about Novice Convene:</h4>
                    <ul class="space-y-2 list-disc list-inside">
                        <li>5★ Resonator rate: <span class="font-bold">0.8%</span></li>
                        <li>4★ Resonator/Weapon rate: <span class="font-bold">6.0%</span></li>
                    </ul>
                    <p>No Resonators or Weapons have an increased rate on this Convene.</p>
                    <p>Only Lustrous Tides can be used on this banner.</p>

                    <div>
                        <p class="font-bold">Available 5★ Resonators:</p>
                        <p>Verina, Encore, Calcharo, Lingyang & Jianxin</p>
                    </div>
                </div>

                <div class="p-4 border-t">
                    <button data-modal-hide="detail-note-modal" type="button"
                        class="w-full py-2 text-black bg-white rounded-lg hover:bg-blue-100">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>