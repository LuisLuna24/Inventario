<div>
    <x-w-alert title="Producto seleccionado" info class="mb-6">
        <x-slot name="slot" class="italic">
            <p>
                <span class="text-bold">Nombre: </span>{{ $product->name }}
            </p>
            @if ($product->sku)
                <p>
                    <span class="text-bold">Codigo: </span>{{ $product->sku }}
                </p>
            @endif
            @if ($product->barcode)
                <p>
                    <span class="text-bold">Codigo: </span>{{ $product->barcode }}
                </p>
            @endif
            <p>
                <span class="text-bold">Stock: </span>{{ $product->stock }}
            </p>
        </x-slot>
    </x-w-alert>

    <x-w-card class="mb-12">
        <div class="grid grid-cols-2 gap-4">
            <x-w-input label="Fecha Inical" type="date" wire:model.live="fecha_inicial" />
            <x-w-input label="Fecha Final" type="date" wire:model.live="fecha_final" />

            <x-w-select class="col-span-1 md:col-span-2" label="Almacen" placeholder="Seleccione un almacen"
                wire:model.live="warehouse_id" :options="$warehouses->select('id', 'name')" option-value="id" option-label="name" />
        </div>
    </x-w-card>

    <h2 class="text-lg font-semibold mb-4">
        Kardex de Productos
    </h2>

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 shadow-lg">
        <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                    <th rowspan="2"
                        class="px-4 py-3 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-300 font-semibold text-left sticky left-0 z-10">
                        Detalle
                    </th>

                    <th colspan="3"
                        class="px-4 py-2 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 font-bold border-b border-emerald-200 dark:border-emerald-800">
                        Entradas
                    </th>

                    <th colspan="3"
                        class="px-4 py-2 bg-red-100 dark:bg-red-900/40 text-red-700 dark:text-red-400 font-bold border-b border-red-200 dark:border-red-800">
                        Salidas
                    </th>

                    <th colspan="3"
                        class="px-4 py-2 bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-400 font-bold border-b border-blue-200 dark:border-blue-800">
                        Balance
                    </th>

                    <th rowspan="2"
                        class="px-4 py-3 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-300 font-semibold">
                        Fecha
                    </th>
                </tr>

                <tr class="text-xs uppercase tracking-wider divide-x divide-gray-200 dark:divide-gray-700">
                    <th
                        class="px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-300 font-medium text-right">
                        Cant.</th>
                    <th
                        class="px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-300 font-medium text-right">
                        Costo</th>
                    <th
                        class="px-3 py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-300 font-medium text-right">
                        Total</th>

                    <th
                        class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 font-medium text-right">
                        Cant.</th>
                    <th
                        class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 font-medium text-right">
                        Costo</th>
                    <th
                        class="px-3 py-2 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-300 font-medium text-right">
                        Total</th>

                    <th
                        class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 font-medium text-right">
                        Cant.</th>
                    <th
                        class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 font-medium text-right">
                        Costo</th>
                    <th
                        class="px-3 py-2 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-300 font-medium text-right">
                        Total</th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($inventories as $inventory)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 group">

                        <td
                            class="px-4 py-3 font-medium text-gray-900 dark:text-white whitespace-nowrap sticky left-0 bg-white dark:bg-gray-900 group-hover:bg-gray-50 dark:group-hover:bg-gray-800">
                            {{ $inventory->detail }}
                        </td>

                        <td
                            class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-900/10">
                            {{ $inventory->cuantity_in }}</td>
                        <td
                            class="px-3 py-3 text-right text-emerald-700 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-900/10">
                            {{ $inventory->cost_in }}</td>
                        <td
                            class="px-3 py-3 text-right font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50/30 dark:bg-emerald-900/10">
                            {{ $inventory->total_in }}</td>

                        <td class="px-3 py-3 text-right text-red-700 dark:text-red-400 bg-red-50/30 dark:bg-red-900/10">
                            {{ $inventory->cuantity_out }}</td>
                        <td class="px-3 py-3 text-right text-red-700 dark:text-red-400 bg-red-50/30 dark:bg-red-900/10">
                            {{ $inventory->cost_out }}</td>
                        <td
                            class="px-3 py-3 text-right font-semibold text-red-700 dark:text-red-400 bg-red-50/30 dark:bg-red-900/10">
                            {{ $inventory->total_out }}</td>

                        <td
                            class="px-3 py-3 text-right text-blue-700 dark:text-blue-400 bg-blue-50/30 dark:bg-blue-900/10">
                            {{ $inventory->quantity_balance ?? $inventory->total_balnce }}</td> {{-- Corregí nombre variable --}}
                        <td
                            class="px-3 py-3 text-right text-blue-700 dark:text-blue-400 bg-blue-50/30 dark:bg-blue-900/10">
                            {{ $inventory->cost_balance }}</td>
                        <td
                            class="px-3 py-3 text-right font-bold text-blue-800 dark:text-blue-300 bg-blue-50/30 dark:bg-blue-900/10">
                            {{ $inventory->total_balance }}</td>

                        <td class="px-4 py-3 text-center text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">
                            {{ $inventory->created_at->format('Y-m-d') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11"
                            class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900">
                            <div class="flex flex-col items-center justify-center">
                                <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>
                                <span class="text-base font-medium">Sin movimientos registrados</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $inventories->links() }}
    </div>
</div>
