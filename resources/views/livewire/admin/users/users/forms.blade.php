<div>
    <x-w-card>
        <form class="space-y-4" method="POST" wire:submit.prevent="save">
            @csrf

            <x-w-input label="Nombre" wire:model="name" placeholder="Nombre completo del usuario" />

            <x-w-input type="email" label="Correo" wire:model="email" placeholder="Correo del usuario" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-password type="password" label="Contraseña" placeholder="**********" wire:model="password" />

                <x-w-password type="password" label="Repetir contraseña" placeholder="**********"
                    wire:model="password_confirmation" />
            </div>

            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
