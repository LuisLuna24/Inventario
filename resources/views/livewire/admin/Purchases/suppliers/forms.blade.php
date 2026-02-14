<div>
    <x-w-card>
        <form class="space-y-4" method="POST" wire:submit.prevent="save">
            @csrf

            <x-w-input label="Razón Social" wire:model="name" placeholder="Razón social del proveedor" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-select label="Tipo Doc" placeholder="Tipo de documento" wire:model="identity_id" :options="$identities"
                    option-label="name" option-value="id" />

                <x-w-input label="Numéro del documento" wire:model="document_number"
                    placeholder="Numéro del documento del proveedor" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-input type="email" label="Correo" wire:model="email" placeholder="Correo del proveedor" />

                <x-w-input label="Teléfono" wire:model="phone" placeholder="Teléfono del proveedor" />
            </div>

            <x-w-textarea label="Dirección" wire:model="address" placeholder="Dirección del proveedor">
            </x-w-textarea>

            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
