<div>
    <div>
        <!-- Banner Cards -->
        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">
            <!-- Standard Banner -->
            <a href="{{ route('standard-banner') }}" class="relative cursor-pointer group">
                <img src="{{ asset('storage/images/background/gacha-banner-2.jpg') }}" alt="Standard Banner"
                    class="w-full transition-transform duration-300 rounded-lg shadow-lg group-hover:scale-105">
                <div class="absolute bottom-0 w-full p-2 text-center text-white bg-black bg-opacity-50">
                    Standard Banner
                </div>
            </a>

            <!-- Weapon Banner -->
            <a href="#" class="relative cursor-pointer group">
                <img src="{{ asset('storage/images/background/gacha-banner-2.jpg') }}" alt="Weapon Banner"
                    class="w-full transition-transform duration-300 rounded-lg shadow-lg group-hover:scale-105">
                <div class="absolute bottom-0 w-full p-2 text-center text-white bg-black bg-opacity-50">
                    Weapon Banner
                </div>
            </a>

            <!-- Limited Banner -->
            <a href="#" class="relative cursor-pointer group">
                <img src="{{ asset('storage/images/background/gacha-banner-2.jpg') }}" alt="Limited Banner"
                    class="w-full transition-transform duration-300 rounded-lg shadow-lg group-hover:scale-105">
                <div class="absolute bottom-0 w-full p-2 text-center text-white bg-black bg-opacity-50">
                    Limited Banner
                </div>
            </a>
        </div>


        <!-- Informasi Section -->
        <div class="p-6 mt-6 text-white bg-gray-800 rounded-lg shadow-lg">
            <h2 class="mb-4 text-xl font-semibold">Informasi Menarik</h2>
            <ul class="pl-5 space-y-2 list-disc">
                <li>Total Pull: <span class="font-bold">120</span></li>
                <li>Rate ★5: <span class="font-bold">1.5%</span></li>
                <li>Update Terbaru: <span class="text-yellow-400">Event Banner Baru!</span></li>
            </ul>
        </div>
    </div>

</div>