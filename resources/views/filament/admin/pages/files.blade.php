<x-filament-panels::page>
    <div class="flex flex-col justify-center overflow-hidden py-6 sm:py-12">
        <div class="mx-auto px-4 w-full">
            <div class="grid w-full sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach($this->paginate as $dir)
                        <div
                            class="relative flex flex-col shadow-md rounded-xl overflow-hidden hover:shadow-lg hover:-translate-y-1 transition-all duration-300 max-w-sm border border-gray-300">
                            <div class="h-auto overflow-hidden">
                                <div class="h-52 justify-center overflow-hidden relative">
                                    <img src="{{ asset('uploads/'.$dir->getFilename()) }}" alt="image"
                                         class="object-cover"
                                         lazy="loading">
                                </div>
                            </div>
                            <div class="bg-white py-4 px-3">
                                <h3 class="text-xs mb-2 font-medium">{{$dir->getFilename() }}</h3>
                                <div class="flex justify-between items-center">
                                    <p class="text-xs text-gray-400">
                                        Size: {{ Illuminate\Support\Number::fileSize($dir->getSize(),2) }}
                                    </p>
                                    <div class="relative z-40 flex items-center gap-2">
                                        <x-filament::icon-button
                                            icon="tabler-download"
                                            color="info"/>
                                        <x-filament::icon-button
                                            icon="tabler-trash"
                                            label="New label"
                                            color="danger"
                                            wire:click="delete('{{$dir->getFilename()}}')"
                                            wire:confirm="Are you sure you want to delete this image?"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
            </div>
        </div>
    </div>
</x-filament-panels::page>
