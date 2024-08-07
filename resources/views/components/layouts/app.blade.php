<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-layouts.header></x-layouts.header>

<body class="font-sans antialiased dark:bg-black dark:text-white">
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50 ">
        {{-- <img id="background" class="absolute -left-20 top-0 max-w-[877px]"
            src="https://laravel.com/assets/img/welcome/background.svg" /> --}}
            <img id="background" class="absolute opacity-50 inset-0 bg-cover bg-center bg-no-repeat"
            src="{{Storage::url('public/images/background/T_Bgloadin12_UI.png');}}"/>
        <div
            class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid items-center grid-cols-2 py-3 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        {{-- <nav>
                            <a href="http://" target="_blank" rel="noopener noreferrer">Bag</a>
                            <a href="http://" target="_blank" rel="noopener noreferrer">Reset</a>
                        </nav> --}}
                    </div>
                </header>
                {{ $slot }}
                <!-- Include the Inventory Component -->


            </div>
        </div>
    </div>
</body>

<x-layouts.footer></x-layouts.footer>
</html>
