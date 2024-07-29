<div id="default-modal" tabindex="-1" aria-hidden="true"
    class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full p-4">
        <!-- Modal content -->
        <div class="relative bg-white shadow rounded-2xl">
            <!-- Modal header -->
            <div class="flex items-center justify-between p-4 border-b rounded-t md:p-5 dark:border-black">
                <h3 class="text-xl font-semibold text-black">
                    Inventory List
                </h3>
                <button type="button"
                    class="inline-flex items-center justify-center w-8 h-8 text-sm text-gray-400 bg-transparent rounded-lg hover:bg-gray-200 hover:text-gray-900 ms-auto dark:hover:bg-gray-600 dark:hover:text-white"
                    data-modal-hide="default-modal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-4 space-y-4 md:p-5">
                <div id="inventory" class="mt-8">
                    <div class="grid grid-cols-5 gap-4">
                        @foreach ($inventoryItems as $item)
                            <div class="p-4 border border-gray-300 rounded-2xl">
                                <img src="{{ Storage::url($item->img) }}" alt="{{ $item->name }}" class="object-cover w-full h-32 rounded-lg">
                                <h3 class="mt-2 text-lg font-semibold">{{ $item->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $item->count }}</p>
                                <p class="text-yellow-400">{{ str_repeat('★', $item->rarity) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="flex items-center p-4 border-t border-gray-200 rounded-b md:p-5 dark:black">
                <button data-modal-hide="default-modal" type="button"
                    class="py-2.5 px-5 ms-3 text-sm font-medium text-gray-900  focus:outline-none bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:z-10 focus:ring-4 focus:ring-gray-100 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700">Got
                    It</button>
            </div>
        </div>
    </div>
</div>

<div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="flex items-center bg-gray-100 p-4 rounded-lg">
      <img src="{{ Storage::url($item->img) }}" alt="{{ $item->name }}" alt="Item" class="w-12 h-12 mr-4 rounded-full">
      <div>
        <p class="text-sm font-medium text-gray-700">{{ $item->name }}</p>
        <p class="text-yellow-500">{{ str_repeat('★', $item->rarity) }}</p>
      </div>
    </div>
    <!-- Repeat for other items -->
</div>
