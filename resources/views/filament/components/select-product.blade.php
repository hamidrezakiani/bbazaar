<div class="flex items-center">
    <div class="px-2 ">
        <div class="h-6 w-auto">
            <img src="@if ($image != null) {{ asset('uploads/thumb-'.$image) }} @else {{ asset('default-image.webp') }} @endif" alt="product-image" role="img"
                class="h-full w-full overflow-hidden object-cover inline-flex items-center" />
        </div>
    </div>

    <div class="flex flex-col justify-center pl-3">
        <p class="text-xs pb-1">{{ substr($name, 0, 60) }}</p>
    </div>
</div>
