<x-layouts.layout>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]"
            src="https://laravel.com/assets/img/welcome/background.svg" />
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid items-center grid-cols-2 py-3 lg:grid-cols-3">
                    <div class="flex lg:justify-end lg:col-start-2">
                        

                    </div>
                </header>

                <main>
                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-12">
                        <div id="gachaContainer" class="flex flex-row items-center justify-center gap-3 lg:grid-cols-2">
                            <div id="bannerArea" class="flex items-start ">
                                {{-- <div id="closeBtnArea" class="flex justify-end">
                                    <button id="closeButton" class="items-end p-2 m-2 text-white bg-gray-800 rounded-full focus:outline-none"><svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                                </div> --}}
                                <div id="docs-card"
                                    class="w-max items-start overflow-hidden rounded-2xl bg-white shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                                    <!-- Loading indicator -->
                                    <div id="loadingText" class="flex items-center justify-center"
                                        style="display: none;">
                                        <div
                                            class="w-16 h-16 border-t-2 border-b-2 border-gray-400 rounded-full animate-spin">
                                        </div>
                                    </div>

                                    <div id="gachaResult" class="justify-center gap-2">
                                        <!-- Kartu-kartu hasil gacha akan ditambahkan di sini menggunakan JavaScript -->

                                    </div>
                                    <div id="bg-img" class="max-w-2xl ">
                                        <img class="rounded-2xl w-max" src="{{ $bgImg }}" loading="lazy"
                                            alt="bg-img">
                                    </div>
                                </div>

                            </div>
                            <div id="statusArea" class="flex flex-col items-end ml-5">
                                <div class="flex items-center justify-between w-auto">
                                    <div class="pt-3 mr-5 sm:pt-5">
                                        <h2 class="text-xl font-semibold text-black dark:text-white">Pull Status
                                        </h2>
                                        <div id="pullStatus">
                                            <ul class="ml-4 list-disc">
                                                <li>Total Summons: {{ $cachedData['totalPulls'] }}</li>
                                                <li>Summons since last 4★ or higher:
                                                    {{ $cachedData['pitty4'] }}</li>
                                                <li>Summons since last 5★: {{ $cachedData['pitty5'] }}</li>
                                            </ul>
                                        </div>
                                        <div id="nav-bar" class="flex justify-between p-5 mt-5 w-flex">
                                            <ul class="grid grid-cols-3">
                                                <li class="grid m-2 place-items-center">
                                                    <a href="http://" target="_blank"
                                                        class="flex items-center text-xl font-semibold hover:text-blue-500"
                                                        rel="noopener noreferrer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-black cursor-pointer dark:text-white focus:text-blue-700 focus:outline-none ">Inventory</span>
                                                    </a>
                                                </li>
                                                <li class="grid place-items-center">
                                                    <a href="{{ route('gacha.reset') }}"
                                                        class="flex items-center text-xl font-semibold hover:text-blue-500"
                                                        rel="noopener noreferrer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-black cursor-pointer dark:text-white focus:text-blue-700 focus:outline-none ">Reset</span>
                                                    </a>
                                                </li>
                                                <li class="grid place-items-center">
                                                    <a href="http://" target="_blank"
                                                        class="flex items-center text-xl font-semibold hover:text-blue-500"
                                                        rel="noopener noreferrer">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                                                            class="size-6">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m5.231 13.481L15 17.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v16.5c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Zm3.75 11.625a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                                        </svg>
                                                        <span
                                                            class="text-sm font-semibold text-black cursor-pointer dark:text-white focus:text-blue-700 focus:outline-none ">Detals</span>
                                                    </a>
                                                </li>

                                            </ul>
                                        </div>
                                        <div class="flex justify-end pt-3 ml-8 sm:pt-5">
                                            <form id="gachaForm" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 text-xl font-semibold text-black text-gray-900 bg-gray-100 rounded cursor-pointer dark:text-white hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">
                                                    Single Pull
                                                </button>
                                            </form>
                                            <form id="gacha-ten-pull" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="px-4 py-2 text-xl font-semibold text-black text-gray-900 bg-gray-100 rounded cursor-pointer dark:text-white hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">
                                                    10x Pulls
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>
                    </div>
                </main>
                <x-layouts.footer></x-layouts>
            </div>
        </div>
    </div>
</x-layouts.layout>
