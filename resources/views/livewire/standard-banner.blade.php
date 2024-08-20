<main>
    <div class="grid gap-6 lg:grid-cols-1 lg:gap-12">
        <div id="gachaContainer" class="flex flex-col items-center justify-center gap-3 lg:grid-cols-1">
            <div class="flex flex-col items-center justify-center gap-3">
                <div id="bannerArea" wire:loading.attr="disabled" wire:loading.class="relative opacity-50"
                    class="flex flex-col items-center justify-center" x-data="{ isLoading: false }"
                    x-on:loading.window="isLoading = $event.detail.isLoading">
                    <!-- Loading Indicator -->
                    <div wire:loading.class="absolute flex z-10 items-center justify-center m-0.5 loader"></div>
                    <!-- Gacha Results -->
                    @if ($gachaResults)
                    <div id="gachaResult"
                        class="grid max-w-2xl gap-2 py-2 px-10 m-2 border-2 shadow-xl rounded-xl {{$displayStyle}} weapon-bg h-auto max-w-full bg-cover bg-center bg-no-repeat">
                        <!-- Replace this with your loading spinner -->
                        @foreach ($gachaResults as $item)
                        <div
                            class="relative overflow-hidden bg-gray-700 bg-center border-2 border-solid rounded-lg {{ $item['color'] }}">
                            @if ($item['owned'] == 'no')
                            <div class="absolute top-0 left-0 p-1 text-xs font-bold text-white bg-yellow-500">New</div>
                            @endif
                            <div id="weapon" class="relative">
                                <img class="object-cover w-full border-b-2 max-h-36 " loading="lazy"
                                    src="{{ $item['img'] }}" alt="{{ $item['name'] }}">
                                <div class="absolute bottom-0 right-0">
                                    <p class="text-xl text-yellow-400">{{ str_repeat('★', $item['stars']) }}</p>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-16 p-4">
                                <p class="text-center text-white text-md">{{ $item['name'] }}</p>
                            </div>
                        </div>

                        @endforeach
                    </div>
                    @else
                    <img id="bannerImg" x-show="!isLoading" class="max-w-2xl m-4 shadow-lg rounded-2xl"
                        src="{{ $bgImg }}" alt="">
                    @endif
                </div>
                <div id="statusArea" class="flex w-full font-semibold text-white">
                    <div class="grid w-full grid-cols-2">
                        <div id="pullStatus" class="justify-start">
                            <div id="pullCounter">
                                <ul class="ml-6 list-disc">
                                    <li>Total Summons: {{ $cachedData['totalPulls'] ?? 0 }}</li>
                                    <li>Summons since last 4★ or higher: {{ $cachedData['pity4'] ?? 0 }}</li>
                                    <li>Summons since last 5★: {{ $cachedData['pity5'] ?? 0 }}</li>
                                </ul>
                            </div>

                            <div id="nav-bar" class="flex justify-between mt-2">
                                <ul class="grid grid-cols-3 gap-4">
                                    <li class="grid m-2 place-items-center">
                                        <button type="button" data-modal-target="inventory-modal"
                                            data-modal-toggle="inventory-modal"
                                            class="flex items-center text-sm hover:text-blue-500"
                                            aria-label="Inventory">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                            </svg>
                                            Inventory
                                        </button>
                                        {{-- inventory display --}}
                                    </li>

                                    <li class="grid place-items-center">
                                        <button type="button" wire:click="resetAllRecords()"
                                            x-on:click="$wire.$refresh()"
                                            class="flex items-center text-sm hover:text-blue-500" aria-label="Reset">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            Reset
                                        </button>
                                    </li>
                                    <li class="grid place-items-center">
                                        <button type="button" data-modal-target="detail-note-modal"
                                            data-modal-toggle="detail-note-modal"
                                            class="flex items-center text-sm hover:text-blue-500" aria-label="Detail">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                            </svg>
                                            Detail
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div id="pullsArea" class="grid items-center justify-end grid-cols-2 gap-5">
                            <button type="button" wire:click="singlePull" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                class="px-4 py-2 text-black bg-white rounded-md hover:bg-blue-600 hover:text-white">
                                <span>Single Pull</span>
                            </button>
                            <button type="button" wire:click="tenPulls" wire:loading.attr="disabled"
                                wire:loading.class="opacity-50"
                                class="px-4 py-2 text-black bg-white rounded-md hover:bg-blue-600 hover:text-white">10x
                                Pulls
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Large Modal -->
    {{-- <div x-data="{ open: false }">
        <button x-on:click="open = ! open">Toggle Dropdown</button>

        <div x-show="open" x-transition>
            Dropdown Contents...
        </div>
    </div> --}}
    <div id="inventory-modal" tabindex="-1" aria-hidden="true" x-transition
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full p-4">
            <!-- Modal content -->
            <div class="relative bg-gray-800 border-white shadow rounded-2xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-black">
                    <h3 class="text-xl font-semibold text-white">
                        Inventory List
                    </h3>
                    <button type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="inventory-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <!-- Modal body -->
                @if ($inventoryItems)
                <div class="grid grid-cols-1 gap-6 p-4 md:grid-cols-2 lg:grid-cols-3 ">
                    @foreach ($inventoryItems as $item)
                    <div
                        class="flex items-center p-1 bg-gray-500 border-2 border-yellow-300 border-solid rounded-lg shadow-slate-200">
                        <img src="{{$item->getFirstMediaUrl('gacha','thumb') }}" alt="{{ $item->name }}" alt="Item"
                            class="w-10 mr-4 rounded-full border-slate-900">
                        <div>
                            <p class="text-sm font-medium text-white">{{ $item->name }}</p>
                            <p class="text-yellow-500">
                                @if ( $item->rarity == 1)
                                {{str_repeat('★',5)}}
                                @elseif($item->rarity == 2)
                                {{str_repeat('★',4)}}
                                @else
                                {{str_repeat('★',3)}}
                                @endif</p>
                            <p class="text-sm text-white">{{ $item->count }}x</p>
                        </div>
                    </div>
                    @endforeach
                    <!-- Repeat for other items -->
                </div>
                @else
                <div class="relative gap-6 p-4 text-center item-center">

                    <span>none</span>

                </div>
                @endif

                <!-- Modal footer -->
                <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5 dark:black">
                    <button data-modal-hide="inventory-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900  focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-blue-100 hover:text-black focus:z-10 focus:ring-4 focus:ring-gray-10">Close
                        It</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Large Modal -->
    <div id="detail-note-modal" tabindex="-1" aria-hidden="true"
        class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-2xl max-h-full p-4">
            <!-- Modal content -->
            <div class="relative bg-gray-800 border-white shadow rounded-2xl">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-black">
                    <h3 class="text-xl font-semibold text-white">
                        Rate Details
                    </h3>
                    <button type="button"
                        class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                        data-modal-hide="detail-note-modal">
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 14 14">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </button>
                </div>
                <div class="relative p-7">
                    <span><b> <u>Information about Novice Convene:</u></b></span>
                    <p>
                    <ul>
                        <li>The rate for pulling a 5★ Resonator from this banner is currently set at <u>0.8%.</u> </li>
                        <li>The rate for pulling a 4★ Resonator or Weapon from this banner is currently set at
                            <u>6.0%.</u>.
                        </li>
                    </ul>
                    No Resonators or Weapons have an increased rate on this Convene.
                    You can only use Lustrous Tides on this banner (basic summon ticket).

                    You can only obtain one of the below 5★ Resonators from the Novice Convene: Verina, Encore,
                    Calcharo, Lingyang & Jianxin.
                    </p>
                </div>
                <!-- Modal footer -->
                <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5 dark:black">
                    <button data-modal-hide="detail-note-modal" type="button"
                        class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900  focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-blue-100 hover:text-black focus:z-10 focus:ring-4 focus:ring-gray-10">Close
                        It</button>
                </div>
            </div>
        </div>
    </div>

</main>
