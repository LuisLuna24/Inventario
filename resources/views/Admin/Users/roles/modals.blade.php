<x-modal wire:model.defer="modalPermissions" align="center" blur="sm">
    <x-w-card title="Permisos Asignados al Rol">

        {{-- Preparación de datos (agrupación) al vuelo --}}
        @php
            $groupedPermissions = $permissionsAssign->groupBy(function ($item) {
                return Illuminate\Support\Str::after($item->name, '-');
            });
        @endphp

        <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
            @forelse ($groupedPermissions as $group => $permissions)

                {{-- Contenedor por Módulo --}}
                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-3 border border-gray-100 dark:border-gray-700">

                    {{-- Título del Módulo (Ej: Productos) --}}
                    <div
                        class="flex items-center justify-between mb-3 border-b border-gray-200 dark:border-gray-600 pb-2">
                        <h4 class="text-sm font-bold uppercase text-gray-700 dark:text-gray-300">
                            {{ __(Illuminate\Support\Str::headline($group)) }}
                        </h4>
                        <span class="text-xs text-gray-400 font-mono">
                            {{ $permissions->count() }}
                        </span>
                    </div>

                    {{-- Lista de Badges --}}
                    <div class="flex flex-wrap gap-2">
                        @foreach ($permissions as $permission)
                            @php
                                // Extraer acción para colorear (opcional)
                                $action = Illuminate\Support\Str::before($permission->name, '-');
                                $color = match ($action) {
                                    'create' => 'positive', // Verde
                                    'edit' => 'info', // Azul
                                    'delete' => 'negative', // Rojo
                                    'view' => 'secondary', // Gris
                                    default => 'primary',
                                };
                            @endphp

                            <x-w-badge :color="$color" outline
                                label="{{ __(Illuminate\Support\Str::headline($action)) }}" />
                        @endforeach
                    </div>
                </div>

            @empty
                <div class="flex flex-col items-center justify-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                    <p class="text-sm">{{ __('Este rol no tiene permisos asignados aún.') }}</p>
                </div>
            @endforelse
        </div>

        <x-slot name="footer">
            <div class="flex justify-end gap-x-4">
                {{-- Botón de cerrar simple --}}
                <x-w-button flat label="{{ __('Cerrar') }}" wire:click="closeModal" />
            </div>
        </x-slot>

    </x-w-card>
</x-modal>
