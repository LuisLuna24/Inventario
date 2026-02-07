<div class="max-w-[1800px] mx-auto p-2 space-y-4">

    <x-w-card class="!p-0 overflow-hidden">
        <div class="flex flex-col md:flex-row divide-y md:divide-y-0 md:divide-x dark:divide-gray-700">

            <div class="p-5 md:w-1/3 bg-gray-50 dark:bg-gray-800/50 flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg text-indigo-600 dark:text-indigo-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 dark:text-white leading-tight">
                            {{ $product->name }}
                        </h2>
                        <span class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Kardex Físico -
                            Valorado</span>
                    </div>
                </div>

                <div class="space-y-1 mt-2 text-sm">
                    @if ($product->sku)
                        <div
                            class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1 border-dashed">
                            <span class="text-gray-500">SKU:</span>
                            <span
                                class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ $product->sku }}</span>
                        </div>
                    @endif
                    @if ($product->barcode)
                        <div
                            class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-1 border-dashed">
                            <span class="text-gray-500">Código:</span>
                            <span
                                class="font-mono font-medium text-gray-700 dark:text-gray-300">{{ $product->barcode }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-1">
                        <span class="text-gray-500">Stock Actual Global:</span>
                        <span
                            class="font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2 rounded">
                            {{ $product->stock }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-5 md:w-2/3 bg-white dark:bg-gray-800">
                <h3 class="text-sm font-semibold text-gray-500 uppercase mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                        </path>
                    </svg>
                    Filtros de Búsqueda
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-w-select label="Almacén" placeholder="Todos los almacenes" wire:model.live="warehouse_id"
                        :options="$warehouses->select('id', 'name')" option-value="id" option-label="name" />
                    <x-w-input label="Desde" type="date" wire:model.live="fecha_inicial" />
                    <x-w-input label="Hasta" type="date" wire:model.live="fecha_final" />
                </div>
            </div>
        </div>
    </x-w-card>

    <div
        class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-md bg-white dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs md:text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr class="divide-x divide-gray-200 dark:divide-gray-700">
                        <th rowspan="2"
                            class="px-4 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-left w-32">
                            FECHA / HORA
                        </th>
                        <th rowspan="2"
                            class="px-4 py-3 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 font-bold text-left min-w-[200px]">
                            DETALLE DEL MOVIMIENTO
                        </th>

                        <th colspan="3"
                            class="px-2 py-1 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-bold text-center border-t-4 border-emerald-500">
                            ENTRADAS (+)
                        </th>

                        <th colspan="3"
                            class="px-2 py-1 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 font-bold text-center border-t-4 border-red-500">
                            SALIDAS (-)
                        </th>

                        <th colspan="3"
                            class="px-2 py-1 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 font-bold text-center border-t-4 border-blue-500">
                            SALDO FINAL (=)
                        </th>
                    </tr>

                    <tr class="text-[10px] uppercase tracking-wider divide-x divide-gray-200 dark:divide-gray-700">
                        <th class="px-2 py-2 bg-emerald-50/50 text-emerald-600 text-right">Cant.</th>
                        <th class="px-2 py-2 bg-emerald-50/50 text-emerald-600 text-right">Costo</th>
                        <th class="px-2 py-2 bg-emerald-50/50 text-emerald-600 text-right">Total</th>

                        <th class="px-2 py-2 bg-red-50/50 text-red-600 text-right">Cant.</th>
                        <th class="px-2 py-2 bg-red-50/50 text-red-600 text-right">Costo</th>
                        <th class="px-2 py-2 bg-red-50/50 text-red-600 text-right">Total</th>

                        <th class="px-2 py-2 bg-blue-50/50 text-blue-600 text-right font-bold">Cant.</th>
                        <th class="px-2 py-2 bg-blue-50/50 text-blue-600 text-right">C. Prom</th>
                        <th class="px-2 py-2 bg-blue-50/50 text-blue-600 text-right font-bold">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($inventories as $inventory)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-150 group">

                            <td class="px-4 py-2 whitespace-nowrap text-gray-500 dark:text-gray-400">
                                <div class="font-mono text-xs">{{ $inventory->created_at->format('d/m/Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ $inventory->created_at->format('H:i A') }}
                                </div>
                            </td>

                            <td class="px-4 py-2 text-gray-700 dark:text-gray-300">
                                <span class="font-medium block">{{ $inventory->detail }}</span>
                                <span
                                    class="text-[10px] text-gray-400 uppercase tracking-wide">{{ $inventory->warehouse?->name ?? 'N/A' }}</span>
                            </td>

                            <td
                                class="px-2 py-2 text-right font-mono text-emerald-700 dark:text-emerald-400 bg-emerald-50/20 group-hover:bg-emerald-100/30 tabular-nums">
                                {{ $inventory->quantity_in > 0 ? number_format($inventory->quantity_in, 2) : '-' }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono text-emerald-600 dark:text-emerald-500 bg-emerald-50/20 group-hover:bg-emerald-100/30 tabular-nums text-xs">
                                {{ $inventory->cost_in > 0 ? number_format($inventory->cost_in, 2) : '-' }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono font-semibold text-emerald-700 dark:text-emerald-400 bg-emerald-50/20 group-hover:bg-emerald-100/30 tabular-nums">
                                {{ $inventory->total_in > 0 ? number_format($inventory->total_in, 2) : '-' }}
                            </td>

                            <td
                                class="px-2 py-2 text-right font-mono text-red-700 dark:text-red-400 bg-red-50/20 group-hover:bg-red-100/30 tabular-nums">
                                {{ $inventory->quantity_out > 0 ? number_format($inventory->quantity_out, 2) : '-' }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono text-red-600 dark:text-red-500 bg-red-50/20 group-hover:bg-red-100/30 tabular-nums text-xs">
                                {{ $inventory->cost_out > 0 ? number_format($inventory->cost_out, 2) : '-' }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono font-semibold text-red-700 dark:text-red-400 bg-red-50/20 group-hover:bg-red-100/30 tabular-nums">
                                {{ $inventory->total_out > 0 ? number_format($inventory->total_out, 2) : '-' }}
                            </td>

                            <td
                                class="px-2 py-2 text-right font-mono font-bold text-blue-800 dark:text-blue-300 bg-blue-50/30 group-hover:bg-blue-100/40 tabular-nums border-l border-gray-200 dark:border-gray-700">
                                {{ number_format($inventory->quantity_balance ?? $inventory->total_balnce, 2) }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono text-blue-600 dark:text-blue-400 bg-blue-50/30 group-hover:bg-blue-100/40 tabular-nums text-xs">
                                {{ number_format($inventory->cost_balance, 2) }}
                            </td>
                            <td
                                class="px-2 py-2 text-right font-mono font-bold text-blue-800 dark:text-blue-300 bg-blue-50/30 group-hover:bg-blue-100/40 tabular-nums">
                                {{ number_format($inventory->total_balance, 2) }}
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="11"
                                class="px-6 py-12 text-center text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-900">
                                <div class="flex flex-col items-center justify-center opacity-50">
                                    <svg class="w-16 h-16 mb-4 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    <p class="text-lg font-medium">No se encontraron movimientos</p>
                                    <p class="text-sm">Ajusta los filtros de fecha o almacén.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $inventories->links() }}
    </div>
</div>
