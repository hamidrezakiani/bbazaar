<x-filament::widget>
    <div class="grid gap-x-6 md:grid-cols-2 xl:grid-cols-2">
        <div class="bg-white shadow-sm rounded-xl py-2 px-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div class="flex justify-between items-center bg-white border-b border-b-gray-950/5 dark:bg-gray-900 dark:border-white/10 p-2 mb-1">
                <h2 class="text-base font-semibold leading-6 text-gray-950 dark:text-white">Top Brand
                </h2>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-800 dark:bg-gray-900">
                @foreach ($topBrand as $topB)
                    <li class="@if ($loop->last) pt-2 pb-0 sm:pt-4 @else pb-2 sm:pb-4 mt-2 @endif">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="w-12 h-12 rounded-lg"
                                     src="{{ $topB->image != null
                                        ? asset('uploads/thumb-'.$topB->image)
                                        : asset('uploads/default-image.webp') }}"
                                     alt="brand logo">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                    {{ $topB->title }}
                                </p>
                                <p><span class="font-medium text-danger-700"> Sold: {{ $topB->total }}</span></p>
                            </div>
                            <div
                                    class="inline-flex items-center text-base font-semibold text-danger-700 dark:text-white">
                                {{ Illuminate\Support\Number::format($topB->total_price, maxPrecision: 2) . ' top-category.blade.php' . \App\Helper\Setting::siteSetting()->currency_icon }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
        <div class="bg-white shadow-sm rounded-xl py-2 px-4 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
            <div
                    class="flex justify-between items-center bg-white border-b border-b-gray-950/5 dark:bg-gray-900 dark:border-white/10 p-2 mb-1">
                <h2 class="fi-ta-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">Top
                    Category
                </h2>
            </div>
            <ul class="divide-y divide-gray-200 dark:divide-gray-800 dark:bg-gray-900">
                @foreach ($topCategory as $top)
                    <li class="@if ($loop->last) pt-2 pb-0 sm:pt-4 @else pb-2 sm:pb-4 mt-2 @endif">
                        <div class="flex items-center space-x-4 rtl:space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="w-12 h-12 rounded-lg"
                                     src="{{ $top->image != null
                                        ? asset('uploads/thumb-'.$top->image)
                                        : asset('uploads/default-image.webp') }}"
                                     alt="brand logo">
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-white">
                                    {{ $top->title }}
                                </p>
                                <p><span class="font-medium text-danger-700"> Sold: {{ $top->total }}</span></p>
                            </div>
                            <div
                                    class="inline-flex items-center text-base font-semibold text-danger-700 dark:text-white">
                                {{ Illuminate\Support\Number::format($top->total_price, maxPrecision: 2) . ' top-category.blade.php' . \App\Helper\Setting::siteSetting()->currency_icon }}
                            </div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</x-filament::widget>
