<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-layouts.header></x-layouts.header>

<body class="font-sans antialiased bg-black text-black/50 dark:bg-black dark:text-white">
    <!-- Background Image -->
    <img id="background" class="fixed top-0 left-0 w-full h-full bg-center bg-no-repeat bg-cover opacity-40 z-[-1]"
        src="{{ Storage::url('images/background/T_Bgloadin12_UI.png') }}" />

    <!-- Main Container -->
    <div class="min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
        <div class="w-full px-6 max-w-7xl">
            <!-- Header -->
            <header class="flex items-center justify-between py-4">
                <div class="text-3xl font-bold text-white">WWGacha</div>

                <!-- Navbar -->
                <nav class="flex items-center space-x-6">
                    <a href="/" class="text-white hover:text-gray-300">Main Menu</a>
                    <!-- Dropdown Gacha -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center text-white hover:text-gray-300">
                            Banners
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.outside="open = false"
                            class="absolute right-0 w-48 mt-2 bg-gray-800 border border-gray-700 rounded-lg shadow-lg">
                            {{-- <a href="#" class="block px-4 py-2 text-white hover:bg-gray-700">Limited</a> --}}
                            <a href="{{ url('standard-banner') }}"
                                class="block px-4 py-2 text-white hover:bg-gray-700">Standard Banner</a>
                            <a href="#" class="block px-4 py-2 text-white inactive hover:bg-gray-700">Weapon Banner
                                (Soon)</a>
                        </div>
                    </div>
                    <a href="{{ url('admin') }}" class="text-white hover:text-gray-300">Admin Page</a>
                </nav>
            </header>

            <!-- Main Content -->
            {{ $slot }}

            <!-- Footer -->
            <x-layouts.footer />
        </div>
    </div>
</body>



</html>