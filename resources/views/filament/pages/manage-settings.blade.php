<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div style="margin-top: 1.5rem; display: flex; gap: 1rem; align-items: center; justify-content: flex-end;">
            <x-filament::button color="gray" wire:click="resetForm" type="button">
                Reset Changes
            </x-filament::button>

            <x-filament::button type="submit" color="primary">
                Save Changes
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
