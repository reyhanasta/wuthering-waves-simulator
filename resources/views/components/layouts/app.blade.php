<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <x-layouts.header />
</head>

<body class="flex flex-col min-h-screen font-sans antialiased dark:bg-black dark:text-white">
    <div class="relative flex-grow bg-black text-white/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute top-0 left-0 object-cover w-full h-full opacity-50"
            src="{{ Storage::url('images/background/T_Bgloadin12_UI.png') }}" />

        <div
            class="relative flex flex-col items-center justify-center min-h-screen selection:bg-[#FF2D20] selection:text-white">
            <div class="w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid items-center grid-cols-2 py-3 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        {{-- Tambahkan navigasi jika diperlukan --}}
                    </div>
                </header>
                {{ $slot }}
                <!-- Include the Inventory Component -->
            </div>
        </div>

        <x-layouts.footer />
    </div>

</body>

</html>