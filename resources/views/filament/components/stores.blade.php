<div class="relative z-10 -mt-[4.25rem] ml-6 flex flex-wrap items-center gap-3">
    <div>
        <div class="relative p-2">
            <img alt="Furniture Shop"
                 loading="lazy"
                 class="rounded-full h-16"
                 src="@if ($getRecord()->image != null) {{ asset('uploads/thumb-'.$getRecord()->image )}} @else {{ asset('favicon.png') }} @endif"
            >
        </div>
    </div>
    <div class="relative max-w-[calc(100%-104px)] flex-auto pr-4 pt-2"><h3
            class="text-base font-medium leading-none text-muted-black">{{ $getRecord()->name }}</h3>
        <div class="mt-2 flex w-11/12 items-center gap-1 text-xs leading-none">
            <svg width="1em" height="1em" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                 class="shrink-0 text-[#666666]">
                <path opacity="0.2"
                      d="M8 1.5a5 5 0 00-5 5c0 4.5 5 8 5 8s5-3.5 5-8a5 5 0 00-5-5zm0 7a2 2 0 110-4 2 2 0 010 4z"
                      fill="currentColor"></path>
                <path
                    d="M8 4a2.5 2.5 0 100 5 2.5 2.5 0 000-5zm0 4a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0-7a5.506 5.506 0 00-5.5 5.5c0 1.963.907 4.043 2.625 6.016.772.891 1.64 1.694 2.59 2.393a.5.5 0 00.574 0c.948-.7 1.816-1.502 2.586-2.393C12.591 10.543 13.5 8.463 13.5 6.5A5.506 5.506 0 008 1zm0 12.875c-1.033-.813-4.5-3.797-4.5-7.375a4.5 4.5 0 019 0c0 3.577-3.467 6.563-4.5 7.375z"
                    fill="currentColor"></path>
            </svg>
            <p class="truncate text-base-dark">{{ $getRecord()->address }}</p></div>
        <div class="mt-2 flex w-11/12 items-center gap-1 text-xs leading-none">
            <svg width="1em" height="1em" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"
                 class="shrink-0 text-[#666666]">
                <path opacity="0.2"
                      d="M13.996 10.88A3.022 3.022 0 0111 13.5 8.5 8.5 0 012.5 5a3.02 3.02 0 012.62-2.996.5.5 0 01.519.3l1.32 2.95a.5.5 0 01-.04.47L5.581 7.312a.496.496 0 00-.033.489c.517 1.058 1.61 2.138 2.672 2.65a.494.494 0 00.489-.037l1.563-1.33a.5.5 0 01.474-.044l2.947 1.32a.5.5 0 01.302.52z"
                      fill="currentColor"></path>
                <path
                    d="M13.898 9.904l-2.944-1.32-.008-.003a1 1 0 00-.995.122L8.429 10c-.963-.468-1.958-1.456-2.426-2.407L7.3 6.05a1 1 0 00.118-.99v-.007l-1.323-2.95a1 1 0 00-1.038-.594A3.516 3.516 0 002 5c0 4.963 4.038 9 9 9a3.516 3.516 0 003.492-3.057 1 1 0 00-.594-1.04zM11 13a8.009 8.009 0 01-8-8 2.512 2.512 0 012.18-2.5v.007l1.312 2.938L5.2 6.991a1 1 0 00-.098 1.03c.566 1.158 1.733 2.316 2.904 2.882a1 1 0 001.03-.107l1.52-1.296 2.937 1.316h.007A2.513 2.513 0 0111 13z"
                    fill="currentColor"></path>
            </svg>
            <p class="truncate text-xs text-base-dark">{{ $getRecord()->phone }}</p></div>
    </div>
</div>

