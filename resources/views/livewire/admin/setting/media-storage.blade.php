<div>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button wire:click="submit">Submit</x-filament::button>
        </div>
    </form>
</div>
