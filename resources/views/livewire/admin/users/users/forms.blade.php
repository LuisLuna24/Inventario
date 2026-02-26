<div>
    <x-w-card title="{{ $typeForm == 1 ? 'Crear Usuario' : 'Editar Usuario' }}">
        <form class="space-y-4" method="POST" wire:submit.prevent="save">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-w-input label="Nombre" wire:model="name" placeholder="Nombre completo del usuario" />
                <x-w-input type="email" label="Correo" wire:model="email" placeholder="Correo del usuario" />
            </div>

            {{-- Nuevo campo de Selección de Rol --}}
            <div class="w-full">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rol de Usuario</label>
                <x-w-select placeholder="Seleccione un rol" wire:model="selectedRole" :options="$roles"
                    option-label="name" option-value="name" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-password type="password" label="Contraseña" placeholder="**********" wire:model="password" />
                <x-w-password type="password" label="Repetir contraseña" placeholder="**********"
                    wire:model="password_confirmation" />
            </div>

            <div class="flex justify-end gap-2">
                <x-w-button type="submit" blue spinner="save">Guardar Usuario</x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
