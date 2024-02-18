<div>
    <form wire:submit.prevent="submit">
        <x-filament::section>
            <x-slot name="heading">
                Miscellaneous Setting
            </x-slot>
            {{ $this->form }}
            <div class="mt-6">
                <x-filament::button wire:click="submit">Submit</x-filament::button>
            </div>
        </x-filament::section>
    </form>
    <div class="mt-4">
        {{ $this->table }}
    </div>

    <x-filament-actions::modals/>
</div>
