<div class="flex rounded-md relative">
    <div class="flex">
        <div class="px-2 py-1.50">
            <div class="h-8 w-auto">
                <img src="@if ($image != null) {{ asset('uploads/thumb-'.$image) }} @else {{ asset('default-image.webp') }} @endif"
                     alt="sub-category-image"
                     role="img"
                     class="h-full w-full overflow-hidden object-cover" />
            </div>
        </div>

        <div class="flex flex-col justify-center pl-4 py-1.5">
            <p class="text-sm"><span class="font-bold">{{ $name }}</span></p>
        </div>
    </div>
</div>
