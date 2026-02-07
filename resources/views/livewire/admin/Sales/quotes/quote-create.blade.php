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
                    <h2 class="text-xl font-bold text-gray-700 dark:text-gray-200">Catálogo para Cotizar</h2>

                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar producto..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            $hasStock = $stock > 0;
                            $hasWarehouse = !empty($warehouse_id);
                        @endphp

                        <div wire:click="addFromCard({{ $item->id }})" wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm hover:shadow-md cursor-pointer transition-all active:scale-95 flex flex-col justify-between h-full
                            {{ !$hasWarehouse ? 'border-gray-200 dark:border-gray-700' : ($hasStock ? 'border-green-200 dark:border-green-900/30 hover:border-green-500' : 'border-orange-200 dark:border-orange-900/30 hover:border-orange-400') }}">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-blue-50 text-blue-700 dark:bg-blue-900/50 dark:text-blue-200 text-xs font-bold px-2 py-1 rounded">
                                        ${{ number_format($item->price, 2) }}
                                    </span>

                                    @if (!$hasWarehouse)
                                        <span class="text-[10px] text-gray-400">?</span>
                                    @elseif($hasStock)
                                        <span class="text-[10px] font-bold text-green-600 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            {{ $stock }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-orange-500 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> 0
                                        </span>
                                    @endif
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-1 truncate">{{ $item->sku }}</p>
                            </div>

                            <div
                                class="absolute inset-0 flex items-center justify-center bg-blue-600/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl pointer-events-none">
                                <span
                                    class="bg-blue-600 text-white rounded-full p-1.5 shadow-lg transform scale-0 group-hover:scale-100 transition-transform duration-200">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-400">
                            No hay productos que coincidan.
                        </div>
                    @endforelse
                </div>
                <div class="mt-3">
                    {{ $catalog->links() }}
                </div>
            </x-w-card>
        </div>

        <div class="lg:w-2/5 space-y-4">
            <x-w-card title="Detalles de Cotización">
                <form wire:submit.prevent="save" class="space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <x-w-select label="Almacén (Ref.)" placeholder="Seleccionar..." wire:model.live="warehouse_id"
                            :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']" option-label="name" option-value="id" :clearable="false" />
                        <x-w-input type="date" wire:model="date" label="Válido hasta" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <x-w-native-select label="Tipo Doc." wire:model="voucher_type">
                            <option value="1">Factura</option>
                            <option value="2">Nota</option>
                        </x-w-native-select>
                        <x-w-input wire:model="serie" label="Serie" disabled />
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
                        class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl space-y-3 border border-gray-100 dark:border-gray-700">
                        <x-w-select label="Cliente" placeholder="Buscar cliente..." wire:model="customer_id"
                            :async-data="['api' => route('api.customers.index'), 'method' => 'POST']" option-label="name" option-value="id" />
                        <x-w-textarea label="Notas" wire:model="observation"
                            placeholder="Condiciones de pago, entrega..." rows="2" />

                        <div class="flex justify-between items-end border-t pt-2 dark:border-gray-700">
                            <span class="text-xs font-bold text-gray-500 uppercase">Total Estimado</span>
                            <span class="text-3xl font-black text-blue-600">
                                $<span x-text="total.toFixed(2)"></span>
                            </span>
                        </div>
                    </div>

                    <x-w-button type="submit" spinner="save" primary xl class="w-full shadow-lg">
                        GENERAR COTIZACIÓN
                    </x-w-button>

                    <x-input-error for="products" />
                </form>
            </x-w-card>
        </div>
    </div>
</div>
