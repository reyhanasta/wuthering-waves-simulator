<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-layouts.header></x-layouts.header>

<body class="font-sans antialiased bg-gray-100 text-black/50 dark:bg-black dark:text-white">
    <!-- Background Image -->
    <img id="background" class="fixed top-0 left-0 w-full h-full bg-center bg-no-repeat bg-cover opacity-40 z-[-1]"
        src="{{ Storage::url('images/background/T_Bgloadin12_UI.png') }}" />

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
        <div class="w-full px-6 max-w-7xl">
            <!-- Header -->
            <header class="grid items-center grid-cols-2 py-3 lg:grid-cols-3">
                <div class="flex justify-center lg:col-span-3">
                    {{-- <nav>
                        <a href="http://" target="_blank" rel="noopener noreferrer">Bag</a>
                        <a href="http://" target="_blank" rel="noopener noreferrer">Reset</a>
                    </nav> --}}
                </div>
            </header>

            <!-- Main Content -->
            {{ $slot }}

            <!-- Footer -->
            <x-layouts.footer />
        </div>
    </div>
</body>



</html>