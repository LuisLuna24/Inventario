<x-w-modal-card title="Stock por almacén" wire:model="openModal">
    <ul class="space-y-3">
        @forelse ($inventories as $inventory)
            @php
                // Determinamos el color base según el balance
                $isPositive = $inventory->quantity_balance > 0;
                $color = $isPositive ? 'emerald' : 'red';
            @endphp

            <li
                class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-200">

                <div class="flex items-center gap-3">
                    <div
                        class="p-2 rounded-lg bg-{{ $color }}-50 text-{{ $color }}-600 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">
                        {!! file_get_contents(public_path('svg/building-warehouse.svg')) !!}
                    </div>

                    <div>
                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                            {{ $inventory->warehouse->name }}
                        </h4>

                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            {{ $inventory->warehouse->location }}
                        </p>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-1">
                        Disponible
                    </p>
                    <p class="text-2xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">
                        {{ $inventory->quantity_balance }}
                    </p>
                </div>
            </li>
        @empty
            <li
                class="flex flex-col items-center justify-center p-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600">
                <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mb-2" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <p class="text-gray-500 dark:text-gray-400 font-medium">No hay registros de inventario</p>
            </li>
        @endforelse
    </ul>
</x-w-modal-card>


