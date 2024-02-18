<x-filament-panels::page>
    <form wire:submit.prevent="update">
        {{ $this->form }}
        <section class="flex justify-end gap-x-2 gap-y-4 mt-6">
            <x-filament::button type="submit">Update Setting</x-filament::button>
            <x-filament::button color="gray">Cancel</x-filament::button>
        </section>

    </form>
    <x-filament::section aside class="mt-6">
        <x-slot name="heading">
            Shop Language
        </x-slot>
        <x-slot name="description">
            This is all the information we hold about the user.
        </x-slot>
        {{ $this->table }}
    </x-filament::section>
</x-filament-panels::page>
