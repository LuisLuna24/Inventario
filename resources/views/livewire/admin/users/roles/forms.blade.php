<div>
    <div class="bg-white dark:bg-gray-700 shadow rounded-lg p-6">

        <div class="mb-6">
            {{-- Asegúrate que x-w-input soporte wire:model.live si quieres validación en tiempo real --}}
            <x-w-input label="Nombre del rol" wire:model="name" />
        </div>

        <div class="border-t border-gray-200 dark:border-gray-700 my-6"></div>

        <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-6">
                {{ __('Asignación de Permisos') }}
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

                @foreach ($groupedPermissions as $groupName => $permissions)
                    {{-- Agregamos wire:key para ayudar a Livewire a rastrear elementos --}}
                    <x-w-card wire:key="group-{{ $groupName }}">
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-200 tracking-wider">
                                {{ __(Str::headline($groupName)) }}
                            </h4>
                        </div>

                        <div class="space-y-2">
                            @foreach ($permissions as $permission)
                                <label class="flex items-start cursor-pointer group"
                                    wire:key="perm-{{ $permission->id }}">
                                    {{--
                                        IMPORTANTE: wire:model.live permite que la función updatedSelectedPermissions
                                        se dispare inmediatamente al hacer click.
                                        Si usas solo wire:model, se disparará hasta la próxima acción de red.
                                    --}}
                                    <x-w-checkbox label="{{ $permission->name }}" wire:model.live="selectedPermissions"
                                        value="{{ $permission->id }}" />
                                </label>
                            @endforeach
                        </div>
                    </x-w-card>
                @endforeach
            </div>

            <x-input-error for="selectedPermissions" class="mt-4" />
        </div>

        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
            <x-w-button wire:click="save" blue class="w-full sm:w-auto flex justify-center">
                {{ __('Guardar') }}
            </x-w-button>
        </div>
    </div>
</div>
