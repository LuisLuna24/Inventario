<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    removeProduct(index) {
        this.products.splice(index, 1)
    },
    init() {
        this.$watch('products', (newProducts) => {
            let total = 0;
            newProducts.forEach(product => {
                total += (product.quantity || 0) * (product.price || 0);
            });
            this.total = total;
        });
    },
}" class="max-w-[1800px] mx-auto p-2">

    <div class="flex flex-col lg:flex-row gap-6">

        <div class="lg:w-3/5 space-y-4">
            <x-w-card>
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Selección de Insumos</h2>
                        <p class="text-xs text-gray-500">Haz clic para agregar a la orden de compra</p>
                    </div>

                    <div class="relative w-full max-w-xs md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar insumo..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            $lowStock = $stock < 5; // Umbral de stock bajo
                            $hasWarehouse = !empty($warehouse_id);
                        @endphp

                        <div wire:click="addFromCard({{ $item->id }})" wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-95 flex flex-col justify-between h-full
                            {{ $lowStock && $hasWarehouse ? 'border-yellow-300 dark:border-yellow-700 ring-1 ring-yellow-100 dark:ring-yellow-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-indigo-400' }}">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[10px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">
                                        {{ $item->sku ?? 'REF' }}
                                    </span>

                                    @if (!$hasWarehouse)
                                        <span class="text-[10px] text-gray-400">?</span>
                                    @elseif($lowStock)
                                        <span
                                            class="text-[10px] font-bold text-yellow-600 bg-yellow-50 px-1 rounded flex items-center gap-1">
                                            ⚠️ Stock: {{ $stock }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-green-600 flex items-center gap-1">
                                            Stock: {{ $stock }}
                                        </span>
                                    @endif
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>

                                @if ($item->cost > 0)
                                    <p class="text-[10px] text-gray-400 mt-1">
                                        Último costo: ${{ number_format($item->cost, 2) }}
                                    </p>
                                @endif
                            </div>

                            <div class="mt-2 flex justify-end">
                                <span
                                    class="text-indigo-600 bg-indigo-50 dark:bg-indigo-900/30 dark:text-indigo-300 p-1 rounded-full group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-400">
                            Sin resultados.
                        </div>
                    @endforelse
                </div>
                <div class="mt-3">
                    {{ $catalog->links() }}
                </div>
            </x-w-card>
        </div>

        <div class="lg:w-2/5 space-y-4">
            <x-w-card title="Datos de Recepción">
                <form wire:submit.prevent="save" class="space-y-4">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="col-span-1 md:col-span-2">
                            <x-w-select label="Proveedor *" placeholder="Seleccionar proveedor"
                                wire:model.live="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']" option-label="name" option-value="id" />
                        </div>

                        <x-w-select label="Almacén Destino *" wire:model.live="warehouse_id" :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']"
                            option-label="name" option-value="id" :disabled="count($products) > 0" />

                        <x-w-select label="Importar Orden" placeholder="Opcional..." wire:model.live="purchase_order_id"
                            :async-data="['api' => route('api.purchase-orders.index'), 'method' => 'POST']" option-label="name" option-value="id" option-description="description" />
                    </div>

                    <div class="grid grid-cols-2 gap-3 border-t pt-3 dark:border-gray-700">
                        <x-w-native-select label="Comprobante" wire:model="voucher_type">
                            <option value="1">Factura</option>
                            <option value="2">Nota / Ticket</option>
                        </x-w-native-select>
                        <x-w-input type="date" wire:model="date" label="Fecha Emisión" />
                    </div>

                    <div
                        class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden bg-gray-50 dark:bg-gray-900 flex flex-col h-[300px]">
                        <div
                            class="grid grid-cols-12 gap-2 bg-gray-100 dark:bg-gray-800 p-2 text-xs font-bold text-gray-500 uppercase">
                            <div class="col-span-6">Concepto</div>
                            <div class="col-span-2 text-center">Cant.</div>
                            <div class="col-span-3 text-right">Total</div>
                            <div class="col-span-1"></div>
                        </div>

                        <div class="overflow-y-auto flex-1 p-2 space-y-2">
                            <template x-for="(product, index) in products" :key="index">
                                <div
                                    class="grid grid-cols-12 gap-2 items-center bg-white dark:bg-gray-800 p-2 rounded shadow-sm border border-gray-100 dark:border-gray-700">

                                    <div class="col-span-6">
                                        <div class="text-xs font-bold text-gray-800 dark:text-gray-200 leading-tight"
                                            x-text="product.name"></div>
                                        <input type="number" x-model.number="product.price" step="0.01"
                                            class="w-20 mt-1 h-6 text-xs border-0 border-b border-gray-200 bg-transparent focus:ring-0 p-0 text-blue-600 font-semibold"
                                            placeholder="Precio">
                                    </div>

                                    <div class="col-span-2">
                                        <x-w-input type="number" x-model.number="product.quantity" min="1"/>
                                    </div>

                                    <div
                                        class="col-span-3 text-right font-bold text-gray-700 dark:text-gray-300 text-sm">
                                        $<span
                                            x-text="((product.quantity || 0) * (product.price || 0)).toFixed(2)"></span>
                                    </div>

                                    <div class="col-span-1 text-center">
                                        <button type="button" @click="removeProduct(index)"
                                            class="text-red-400 hover:text-red-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </template>
                            <template x-if="products.length === 0">
                                <div class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                                    <span class="text-xs italic">Agrega productos del catálogo</span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div
                        class="bg-indigo-50 dark:bg-indigo-900/20 p-4 rounded-xl space-y-3 border border-indigo-100 dark:border-indigo-800">
                        <x-w-textarea label="Observaciones" wire:model="observation"
                            placeholder="Notas de recepción..." rows="1" />

                        <div
                            class="flex justify-between items-end border-t border-indigo-200 dark:border-indigo-700 pt-3">
                            <span class="text-xs font-bold text-indigo-800 dark:text-indigo-300 uppercase">Total
                                Compra</span>
                            <span class="text-3xl font-black text-indigo-700 dark:text-indigo-400">
                                $<span x-text="total.toFixed(2)"></span>
                            </span>
                        </div>
                    </div>

                    <x-w-button type="submit" spinner="save" indigo xl class="w-full shadow-md">
                        REGISTRAR COMPRA
                    </x-w-button>

                    <x-input-error for="products" />
                    <x-input-error for="total" />
                </form>
            </x-w-card>
        </div>
    </div>
</div>
