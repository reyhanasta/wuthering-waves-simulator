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
                    @if (Route::has('login'))
                        <nav class="-mx-3 flex flex-1 justify-end">
                            @auth
                                <a href="{{ url('/dashboard') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                    Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                    Log in
                                </a>

                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}"
                                        class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
                                        Register
                                    </a>
                                @endif
                            @endauth
                        </nav>
                    @endif
                </header>

                <main class="mt-4">
                    <div class="grid gap-6 lg:grid-cols-1 lg:gap-12">
                        <div href="https://laravel.com/docs" id="docs-card"
                            class="flex flex-col items-start gap-6 overflow-hidden rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] md:row-span-3  dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                            <div id="gachaResult" class="mt-6"></div>
                        </div>
                        <div class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                            <div class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">

                            </div>
                            <div class="pt-3 sm:pt-5">
                                <h2 class="text-xl font-semibold text-black dark:text-white">Pull Status</h2>
                                <div id="pullStatus">
                                    <ul class="list-disc ml-4">
                                        <li>Total Summons: {{ Cache::get('totalPulls_count',0) }}</li>
                                        <li>Summons since last 4★ or higher : {{ Cache::get('pitty4_count',0) }} </li>
                                        <li>Summons since last 5★ : {{ Cache::get('pitty5_count',0) }} </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        </a>
                        <div
                            class="flex items-start gap-4 rounded-lg bg-white p-6 shadow-[0px_14px_34px_0px_rgba(0,0,0,0.08)] ring-1 ring-white/[0.05] transition duration-300 hover:text-black/70 hover:ring-black/20 focus:outline-none focus-visible:ring-[#FF2D20] lg:pb-10 dark:bg-zinc-900 dark:ring-zinc-800 dark:hover:text-white/70 dark:hover:ring-zinc-700 dark:focus-visible:ring-[#FF2D20]">
                            <div
                                class="flex size-12 shrink-0 items-center justify-center rounded-full bg-[#FF2D20]/10 sm:size-16">
                            </div>
                            <div class="pt-3 sm:pt-5">
                                <form id="gachaForm" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xl font-semibold text-black dark:text-white rounded flex px-4 py-2 bg-gray-100 text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">Single
                                        Pull</button>
                                </form>
                            </div>
                            <br>
                            <div class="pt-3 sm:pt-5">
                                <form id="gacha-ten-pull" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="text-xl font-semibold text-black dark:text-white rounded flex px-4 py-2 bg-gray-100 text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">10x
                                        Pulls</button>
                                </form>
                            </div>
                            <div class="pt-3 sm:pt-5">
                                <a href="{{route('gacha.reset')}}" class="text-xl font-semibold text-black dark:text-white rounded flex px-4 py-2 text-gray-900 cursor-pointer hover:bg-blue-200 focus:text-blue-700 focus:bg-blue-200 focus:outline-none focus:ring-blue-600">Reset</a>
                            </div>
                        </div>
                    </div>
                </main>
                <x-layouts.footer></x-layouts>
            </div>
        </div>
    </div>
</x-layouts.layout>
