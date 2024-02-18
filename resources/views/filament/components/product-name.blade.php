<div class="flex gap-4 ml-2 space-x-2 pr-8">
    <img class="w-9 h-9 rounded-full"
        src="@if ($getRecord()->image != null) {{ asset('uploads/'.$getRecord()->image) }} @else {{ asset('default-image.webp') }} @endif" />
    <p class="flex flex-col">
        <span class="break-all">{{ $getRecord()->title }} </span>
    </p>
</div>
