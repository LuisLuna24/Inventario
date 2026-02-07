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
                    <h2 class="text-xl font-bold text-gray-700 dark:text-gray-200">Productos</h2>

                    <div class="relative w-full max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar por nombre, código..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            // Lógica de Stock
                            $stock = intval($item->stock ?? 0);
                            $hasStock = $stock > 0;
                            $hasWarehouse = !empty($warehouse_id);
                        @endphp

                        <div wire:key="prod-{{ $item->id }}"
                            @if ($hasStock && $hasWarehouse) wire:click="addFromCard({{ $item->id }})"
                                class="group relative bg-white dark:bg-gray-800 border dark:border-gray-700 rounded-xl p-3 shadow-sm hover:shadow-md hover:border-primary-500 cursor-pointer transition-all active:scale-95 flex flex-col justify-between h-full"
                            @else
                                class="relative bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl p-3 cursor-not-allowed opacity-75 flex flex-col justify-between h-full" @endif>
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300 text-xs font-bold px-2 py-1 rounded">
                                        ${{ number_format($item->price, 2) }}
                                    </span>

                                    @if (!$hasWarehouse)
                                        <span class="text-[10px] text-orange-500 font-bold"
                                            title="Selecciona un almacén">
                                            ¿Almacén?
                                        </span>
                                    @elseif($hasStock)
                                        <span
                                            class="text-[10px] font-bold {{ $stock < 5 ? 'text-yellow-600' : 'text-green-600' }}">
                                            Stock: {{ $stock }}
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-red-600">
                                            AGOTADO
                                        </span>
                                    @endif
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>
                                <p class="text-[10px] text-gray-400 mt-1 truncate">{{ $item->sku ?? 'S/N' }}</p>
                            </div>

                            @if ($hasWarehouse && !$hasStock)
                                <div
                                    class="absolute inset-0 bg-white/60 dark:bg-gray-900/60 z-10 flex items-center justify-center rounded-xl backdrop-blur-[1px]">
                                    <span
                                        class="text-xs font-bold text-red-600 border border-red-200 bg-red-50 px-2 py-1 rounded transform -rotate-12 shadow-sm">
                                        SIN STOCK
                                    </span>
                                </div>
                            @endif

                            @if ($hasStock && $hasWarehouse)
                                <div
                                    class="absolute inset-0 flex items-center justify-center bg-primary-600/5 opacity-0 group-hover:opacity-100 transition-opacity rounded-xl pointer-events-none">
                                    <span
                                        class="bg-primary-600 text-white rounded-full p-1.5 shadow-lg transform scale-0 group-hover:scale-100 transition-transform duration-200">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full flex flex-col items-center justify-center text-gray-400 py-10">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                            <p>No se encontraron productos.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-3">
                    {{ $catalog->links() }}
                </div>
            </x-w-card>
        </div>

        <div class="lg:w-2/5 space-y-4">
            <x-w-card>
                <form wire:submit.prevent="save" class="space-y-4">

                    <div class="grid grid-cols-2 gap-3">
                        <x-w-select label="Almacén *" placeholder="Seleccionar..." wire:model.live="warehouse_id"
                            :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']" option-label="name" option-value="id" :clearable="false" />
                        <x-w-native-select label="Comprobante" wire:model="voucher_type">
                            <option value="1">Factura</option>
                            <option value="2">Nota de Venta</option>
                        </x-w-native-select>
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
                        class="bg-gray-50 dark:bg-gray-900 p-4 rounded-xl space-y-4 border border-gray-100 dark:border-gray-700">

                        <x-w-select label="Cliente" placeholder="Buscar cliente..." wire:model.live="customer_id"
                            :async-data="['api' => route('api.customers.index'), 'method' => 'POST']" option-label="name" option-value="id" />

                        @if ($customer_id)
                            <x-w-select label="Cotización" placeholder="Seleccione un cotización"
                                wire:model.live="quote_id" :async-data="['api' => route('api.quotes.index'), 'method' => 'POST']" option-label="name" option-value="id" />
                        @endif

                        <div class="flex justify-between items-end border-t pt-4 dark:border-gray-700">
                            <div>
                                <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total a
                                    Pagar</span>
                            </div>
                            <div class="text-right">
                                <span class="text-4xl font-black text-primary-600 dark:text-primary-500">
                                    $<span x-text="total.toFixed(2)"></span>
                                </span>
                                <span class="text-xs text-gray-400 font-bold ml-1">MXN</span>
                            </div>
                        </div>
                    </div>

                    <x-w-button type="submit" spinner="save" primary xl
                        class="w-full shadow-lg hover:shadow-xl transition-shadow">
                        <div class="flex items-center justify-center gap-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            COBRAR VENTA
                        </div>
                    </x-w-button>

                    <x-input-error for="products" />
                    <x-input-error for="total" />

                </form>
            </x-w-card>
        </div>
    </div>
</div>
