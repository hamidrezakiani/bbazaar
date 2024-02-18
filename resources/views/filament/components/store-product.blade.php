@php
    $store = App\Models\Store::where('admin_id', $getState())->first();
@endphp
<div class="fi-ta-text grid gap-y-1 px-3 py-4">
    <div class="flex gap-4 ml-2 space-x-2 pr-8 items-center">
        <img class="w-9 h-9 max-w-none object-cover object-center ring-white dark:ring-gray-900 rounded-full"
            src="@if ($getRecord()->image != null) {{ asset('uploads/thumb-'.$getRecord()->image) }} @else {{ asset('default-image.webp') }} @endif" />
        <p class="flex flex-col">
            <span>{{ $store?->name }} </span>
            {{-- <span class="text-xs">{{ $getRecord()->email }}</span> --}}
        </p>
    </div>
</div>
