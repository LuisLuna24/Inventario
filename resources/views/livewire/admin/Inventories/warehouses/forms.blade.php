<div>
    <x-w-card>
        <form class="space-y-4" method="POST" wire:submit.prevent="save">
            @csrf

            <x-w-input label="Nombre" wire:model="name" placeholder="Nombre del almacen" value="{{ old('name') }}" />
            <x-w-textarea label="Ubicación" wire:model="location" placeholder="Ubicación del almacen">
            </x-w-textarea>
            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
