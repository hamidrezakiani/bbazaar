<x-filament::widget>
    <section
        class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
        <header class="fi-section-header flex flex-col gap-3 overflow-hidden sm:flex-row sm:items-center px-6 py-4">
            <div class="grid flex-1 gap-y-1">
                <h3 class="fi-section-header-heading text-base font-semibold leading-6 text-gray-950 dark:text-white">
                    Top Products
                </h3>
            </div>

        </header>
        <div class="fi-section-content-ctn border-t border-gray-200 dark:border-white/10">
            <div class="fi-section-content p-6">
                <div class="grid gap-6 sm:grid-cols-1 xl:grid-cols-1">
                    <div class="mt-4 flex space-x-4 overflow-x-auto px-4 pb-4 sm:px-5 focus:scroll-auto">
                        @foreach($products as $product)
                                <div class="flex w-64 shrink-0 flex-col">
                                    <img class="h-60 w-full rounded-2xl border border-gray-50 dark:border-gray-600 object-cover object-center"
                                         src="{{ $product->image != null
                                         ? asset('uploads/thumb-'.$product->image)
                                        : asset('uploads/default-image.webp') }}" alt="image">
                                    <div class="product-card dark:bg-gray-800 mx-2 -mt-8 grow rounded-2xl p-3">
                                        <div class="mt-2">
                                            <a href="#"
                                               class="text-sm font-medium text-slate-700 line-clamp-1 focus:text-[#4f46e5] dark:text-gray-100">
                                                {{ \Illuminate\Support\Str::limit(\Illuminate\Support\Str::wordWrap(string: $product->title, characters: 15), 70) }}
                                            </a>
                                        </div>
                                        <div class="flex items-end justify-between">
                                            <p class="mt-2">
                                    <span class="text-base text-slate-700 dark:text-gray-100">
                                        <span class="font-semibold">Price:</span>
                                        <span class="text-danger-700">
                                            {{ Illuminate\Support\Number::format($product->total_price_afn, maxPrecision: 2) . '؋' }}
                                        </span>
                                    </span>
                                            </p>
                                            <p class="mt-2">
                                    <span class="text-base text-slate-700 dark:text-gray-100">
                                        <span class="font-semibold">Sold:</span>
                                        <span class="text-danger-700">{{ $product->total }}</span>
                                    </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                  
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-filament::widget>
