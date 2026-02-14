<x-w-card>
    <form class="space-y-4" method="POST" wire:submit.prevent="save">
        @csrf

        <x-w-input label="Nombre" wire:model="name" placeholder="Nombre de la categoría" />
        <x-w-input type="numeric" label="Prcentaje de ganancia" wire:model="porcent"
            placeholder="Porcentaje de ganancia (Eje. 100)" />
        <x-w-textarea label="Descripción" wire:model="description" placeholder="Descripción de la categoría">
        </x-w-textarea>
        <div class="flex justify-end">
            <x-w-button type="submit" blue>Guardar</x-w-button>
        </div>
    </form>
</x-w-card>
