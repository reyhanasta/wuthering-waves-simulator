<main>
    <div class="grid gap-6 lg:grid-cols-1 lg:gap-12">
        <div id="gachaContainer" class="flex flex-col items-center justify-center gap-3 lg:grid-cols-1">
            <div id="bannerArea" class="flex flex-col items-start">
                <img id="bannerImg" class="max-w-2xl shadow-lg rounded-2xl" src="{{ $bgImg }}" alt="">
                <div id="gachaResult" class="max-w-2xl m-5 grid grid-cols-5 grid-rows-1 gap-2 justify-center ">
                    @foreach ($gachaResults as $item)
                        <div id="weapon-list" class="relative p-2 overflow-hidden bg-gray-800 shadow-lg rounded-xl">
                            <div class="absolute top-0 left-0 p-1 text-xs font-bold text-white bg-yellow-500">New</div>
                            <img class="object-cover w-full h-32" src="{{ $item['img'] }}" alt="{{ $item['name'] }}"
                                style="background-color: purple" />
                            <div class="p-2">
                                <div class="flex justify-center">
                                    <span class="text-xs text-yellow-400">

                                        {{ $item['rarity'] === 1 ? '★★★★★' : '' }}
                                        {{ $item['rarity'] === 2 ? '★★★★' : '' }}
                                        {{ $item['rarity'] === 3 ? '★★★' : '' }}

                                    </span>
                                </div>
                                <div class="mt-2 text-center">
                                    <p class="text-white">{{ $item['name'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div id="statusArea" class="flex w-full dark:text-white font-semibold">
                    <div class="grid grid-cols-2 w-full">
                        <div id="pullStatus" class="justify-start">
                            <div id="pullCounter">
                                <ul class="ml-6 list-disc">
                                    <li>Total Summons: {{ $cachedData['totalPulls'] }}</li>
                                    <li>Summons since last 4★ or higher:
                                        {{ $cachedData['pitty4'] }}</li>
                                    <li>Summons since last 5★: {{ $cachedData['pitty5'] }}</li>
                                </ul>
                            </div>

                            <div id="nav-bar" class="flex justify-between mt-2 w-flex">
                                <ul class="grid grid-cols-3">
                                    <li class="grid m-2 place-items-center">
                                        <button type="button"
                                            class="flex items-center text-sm  hover:text-blue-500"
                                            rel="noopener noreferrer"> <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                                stroke="currentColor" class="size-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                            </svg>
                                           Inventory
                                            </button>
                                    </li>
                                    <li class="grid place-items-center">
                                        <button type="button"
                                            class="flex items-center text-sm  hover:text-blue-500"
                                            rel="noopener noreferrer">  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                           Reset
                                            </button>
                                    </li>
                                    <li class="grid place-items-center">
                                        <button type="button"
                                        class="flex items-center text-sm  hover:text-blue-500"
                                        rel="noopener noreferrer">  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="size-6">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                       Detail
                                        </button>

                                    </li>

                                </ul>
                            </div>
                        </div>


                        <div id="pullsArea" class="grid grid-cols-2 justify-end items-center gap-5">
                            <button type="button" wire:click="singlePull">Gacha</button>
                            <button type="button" wire:click="tenPulls">Gacha 10x</button>
                        </div>
                    </div>


                </div>
            </div>

        </div>



    </div>
</main>
