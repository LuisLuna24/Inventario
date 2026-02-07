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

        <div class="lg:w-3/5 space-y-4 relative">

            @if (!$supplier_id)
                <div
                    class="absolute inset-0 z-20 bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl flex flex-col items-center justify-center text-center p-6 border-2 border-dashed border-gray-300">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-full shadow-lg mb-3">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Selecciona un Proveedor</h3>
                    <p class="text-sm text-gray-500">Para ver los productos disponibles y costos, primero selecciona el
                        proveedor en el panel derecho.</p>
                </div>
            @endif

            <x-w-card>
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Productos Disponibles</h2>
                    </div>

                    <div class="relative w-full max-w-xs md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar producto..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500"
                            {{ !$supplier_id ? 'disabled' : '' }}>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            $lowStock = $stock < 10; // Umbral más alto para reabastecimiento
                            $hasWarehouse = !empty($warehouse_id);
                        @endphp

                        <div wire:click="addFromCard({{ $item->id }})" wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-95 flex flex-col justify-between h-full
                            {{ $lowStock && $hasWarehouse ? 'border-red-300 dark:border-red-800 ring-1 ring-red-50 dark:ring-red-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-emerald-400' }}">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 px-1.5 py-0.5 rounded font-mono truncate max-w-[80px]">
                                        {{ $item->sku ?? '---' }}
                                    </span>

                                    @if (!$hasWarehouse)
                                        <span class="text-[10px] text-gray-400">?</span>
                                    @elseif($lowStock)
                                        <span
                                            class="text-[10px] font-bold text-red-600 bg-red-50 dark:bg-red-900/30 px-1 rounded flex items-center gap-1">
                                            Bajo: {{ $stock }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                            Stock: {{ $stock }}
                                        </span>
                                    @endif
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>

                                <p class="text-[10px] text-gray-400 mt-1">
                                    Costo Ref: ${{ number_format($item->cost ?? 0, 2) }}
                                </p>
                            </div>

                            <div class="mt-2 flex justify-end">
                                <span
                                    class="text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300 p-1 rounded-full group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
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
            <x-w-card title="Orden de Compra">
                <form wire:submit.prevent="save" class="space-y-4">

                    <div class="grid grid-cols-1 gap-3">
                        <x-w-select label="Proveedor *" placeholder="Seleccione un proveedor"
                            wire:model.live="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']" option-label="name" option-value="id"
                            :clearable="false" />

                        <div class="grid grid-cols-2 gap-3">
                            <x-w-select label="Almacén *" wire:model.live="warehouse_id" :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']"
                                option-label="name" option-value="id" :disabled="count($products) > 0" />
                            <x-w-input type="date" wire:model="date" label="Fecha" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 border-t pt-3 dark:border-gray-700">
                        <x-w-native-select label="Tipo Comp." wire:model="voucher_type">
                            <option value="1">Factura</option>
                            <option value="2">Nota</option>
                        </x-w-native-select>
                        <div class="grid grid-cols-2 gap-1">
                            <x-w-input wire:model="serie" label="Serie" disable />
                            <x-w-input wire:model="correlative" label="Nº" disable />
                        </div>
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
                        class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-xl space-y-3 border border-emerald-100 dark:border-emerald-800">
                        <x-w-textarea label="Observaciones" wire:model="observation"
                            placeholder="Instrucciones de entrega..." rows="1" />

                        <div
                            class="flex justify-between items-end border-t border-emerald-200 dark:border-emerald-700 pt-3">
                            <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase">Total
                                Estimado</span>
                            <span class="text-3xl font-black text-emerald-700 dark:text-emerald-400">
                                $<span x-text="total.toFixed(2)"></span>
                            </span>
                        </div>
                    </div>

                    <x-w-button type="submit" icon="check" spinner="save" primary xl
                        class="w-full shadow-md !bg-emerald-600 hover:!bg-emerald-700">
                        GENERAR ORDEN
                    </x-w-button>

                    <x-input-error for="products" />
                </form>
            </x-w-card>
        </div>
    </div>
</div>
