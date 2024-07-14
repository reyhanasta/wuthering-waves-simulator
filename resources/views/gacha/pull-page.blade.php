<x-layouts.layout>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]"
            src="https://laravel.com/assets/img/welcome/background.svg" />
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <svg class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" viewBox="0 0 62 65" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                        </svg>
                    </div>
                  
                </header>

                <main class="mt-1">
                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-12">
                        <div id="gachaContainer" class="flex flex-col gap- lg:grid-cols-3">
                            <div id="docs-card" class="flex flex-col items-start gap-12 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                                <div id="gachaResult" class="grid grid-cols-5 gap-2">
                                    <!-- Kartu-kartu hasil gacha akan ditambahkan di sini menggunakan JavaScript -->
                                </div>
                            </div>
                            
                            <div class="flex flex-col items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                                <div class="w-full flex items-center justify-between mb-4">
                                    <div class="pt-3 sm:pt-5">
                                        <h2 class="text-xl font-semibold text-black dark:text-white">Pull Status</h2>
                                        <div id="pullStatus">
                                            <ul class="list-disc ml-4">
                                                <li>Total Summons: {{ Cache::get('totalPulls_count', 0) }}</li>
                                                <li>Summons since last 4★ or higher: {{ Cache::get('pitty4_count', 0) }}</li>
                                                <li>Summons since last 5★: {{ Cache::get('pitty5_count', 0) }}</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form id="gachaForm" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xl font-semibold text-black dark:text-white rounded px-4 py-2 bg-gray-100 text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">
                                                Single Pull
                                            </button>
                                        </form>
                                        <form id="gacha-ten-pull" method="POST">
                                            @csrf
                                            <button type="submit" class="text-xl font-semibold text-black dark:text-white rounded px-4 py-2 bg-gray-100 text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">
                                                10x Pulls
                                            </button>
                                        </form>
                                        <a href="{{ route('gacha.reset') }}" class="text-xl font-semibold text-black dark:text-white rounded px-4 py-2  text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">
                                            Reset
                                        </a>
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
