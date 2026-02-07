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
    }
}" class="max-w-[1800px] mx-auto p-2">

    <div class="flex flex-col lg:flex-row gap-6">

        <div class="lg:w-3/5 space-y-4 relative">

            @if (!$origin_warehouse_id)
                <div
                    class="absolute inset-0 z-20 bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl flex flex-col items-center justify-center text-center p-6 border-2 border-dashed border-gray-300">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-full shadow-lg mb-3 text-gray-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Selecciona Origen</h3>
                    <p class="text-sm text-gray-500">Define desde dónde saldrá la mercancía.</p>
                </div>
            @endif

            <x-w-card>
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Stock en Origen</h2>
                        <p class="text-xs text-gray-400">Selecciona para transferir</p>
                    </div>

                    <div class="relative w-full max-w-xs md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar en inventario..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-gray-500 focus:border-gray-500 transition-colors"
                            {{ !$origin_warehouse_id ? 'disabled' : '' }}>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            $isDisabled = $stock <= 0;
                            $hasOrigin = !empty($origin_warehouse_id);
                        @endphp

                        <div @if (!$isDisabled && $hasOrigin) wire:click="addFromCard({{ $item->id }})" @endif
                            wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm hover:shadow-md transition-all active:scale-95 flex flex-col justify-between h-full"
                            :class="{
                                'cursor-pointer border-gray-200 hover:border-gray-500': {{ !$isDisabled ? 'true' : 'false' }},
                                'cursor-not-allowed border-gray-200 opacity-60': {{ $isDisabled ? 'true' : 'false' }}
                            }">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded font-mono truncate">
                                        {{ $item->sku ?? '---' }}
                                    </span>

                                    <span class="text-[10px] font-bold flex items-center gap-1 px-1.5 py-0.5 rounded"
                                        :class="{
                                            'text-gray-700 bg-gray-50': {{ $stock > 0 ? 'true' : 'false' }},
                                            'text-red-500 bg-red-50': {{ $stock <= 0 ? 'true' : 'false' }}
                                        }">
                                        Disp: {{ $stock }}
                                    </span>
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>
                            </div>

                            @if ($isDisabled && $hasOrigin)
                                <div class="absolute inset-0 flex items-center justify-center z-10 pointer-events-none">
                                    <span
                                        class="bg-white/90 text-gray-500 text-xs font-bold px-2 py-1 rounded border border-gray-200 shadow-sm transform -rotate-12">
                                        AGOTADO
                                    </span>
                                </div>
                            @endif

                            @if (!$isDisabled)
                                <div class="mt-2 flex justify-end">
                                    <span
                                        class="p-1 rounded-full text-gray-600 bg-gray-50 group-hover:bg-gray-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                                        </svg>
                                    </span>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="col-span-full text-center py-10 text-gray-400">
                            Sin resultados en este almacén.
                        </div>
                    @endforelse
                </div>
                <div class="mt-3">
                    {{ $catalog->links() }}
                </div>
            </x-w-card>
        </div>

        <div class="lg:w-2/5 space-y-4">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-t-4 border-gray-500">
                <div
                    class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 rounded-t-xl">
                    <h2 class="text-lg font-bold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                        Nueva Transferencia
                    </h2>
                    <span class="text-xs font-mono text-gray-400"
                        x-text="'{{ $serie }}-' + '{{ $correlative }}'"></span>
                </div>

                <div class="p-4">
                    <form wire:submit.prevent="save" class="space-y-4">

                        <div class="bg-gray-50 dark:bg-gray-900 p-3 rounded-lg space-y-3 border dark:border-gray-700">
                            <x-w-select label="Origen (Desde) *" wire:model.live="origin_warehouse_id" :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']"
                                option-label="name" option-value="id" :disabled="count($products) > 0" />

                            <div class="flex justify-center -my-2 relative z-10">
                                <span class="bg-white dark:bg-gray-800 p-1 rounded-full border text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                </span>
                            </div>

                            <x-w-select label="Destino (Hacia) *" wire:model.live="destination_warehouse_id"
                                :async-data="[
                                    'api' => route('api.warehouses.index'),
                                    'method' => 'POST',
                                    'params' => ['exclude' => $origin_warehouse_id], // Excluimos el origen
                                ]" option-label="name" option-value="id" />
                        </div>

                        <x-w-input type="date" wire:model="date" label="Fecha" />

                        <div
                            class="border border-gray-100 dark:border-gray-700 rounded-lg overflow-hidden flex flex-col h-[300px] bg-gray-50/20">

                            <div
                                class="grid grid-cols-12 gap-2 p-2 text-xs font-bold uppercase border-b bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 border-gray-100">
                                <div class="col-span-6">Producto</div>
                                <div class="col-span-3 text-center">Cant.</div>
                                <div class="col-span-3"></div>
                            </div>

                            <div class="overflow-y-auto flex-1 p-2 space-y-2">
                                <template x-for="(product, index) in products" :key="index">
                                    <div
                                        class="grid grid-cols-12 gap-2 items-center bg-white dark:bg-gray-800 p-2 rounded shadow-sm border border-gray-100 dark:border-gray-700">

                                        <div class="col-span-6">
                                            <div class="text-xs font-bold text-gray-800 dark:text-gray-200 leading-tight"
                                                x-text="product.name"></div>
                                            <div class="text-[9px] text-gray-400">
                                                Max: <span x-text="product.stock_origin"></span>
                                            </div>
                                        </div>

                                        <div class="col-span-3">
                                            <x-w-input type="number" x-model.number="product.quantity" min="0"
                                                x-bind:max="product.stock_origin"/>
                                        </div>

                                        <div class="col-span-3 text-center flex justify-end">
                                            <button type="button" @click="removeProduct(index)"
                                                class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="products.length === 0">
                                    <div
                                        class="h-full flex flex-col items-center justify-center text-gray-400 opacity-60">
                                        <span class="text-xs">Lista vacía</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg space-y-2">
                            <x-w-textarea label="Notas" wire:model="observation"
                                placeholder="Motivo del traslado..." rows="1" />

                            <div class="flex justify-between text-xs text-gray-400 pt-1">
                                <span>Valor Referencial:</span>
                                <span>$<span x-text="total.toFixed(2)"></span></span>
                            </div>
                        </div>

                        <x-w-button type="submit" spinner="save" xl
                            class="w-full shadow-md text-white bg-gray-600 hover:bg-gray-700">
                            REALIZAR TRANSFERENCIA
                        </x-w-button>

                        <x-input-error for="products" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
