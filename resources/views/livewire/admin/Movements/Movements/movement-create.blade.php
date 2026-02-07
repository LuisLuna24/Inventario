<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    type: @entangle('type').live,
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
    get isEntry() { return this.type == 1; }
}" class="max-w-[1800px] mx-auto p-2">

    <div class="flex flex-col lg:flex-row gap-6">

        <div class="lg:w-3/5 space-y-4 relative">

            @if (!$warehouse_id)
                <div
                    class="absolute inset-0 z-20 bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-sm rounded-xl flex flex-col items-center justify-center text-center p-6 border-2 border-dashed border-gray-300">
                    <div class="bg-white dark:bg-gray-800 p-4 rounded-full shadow-lg mb-3">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700 dark:text-gray-200">Selecciona un Almacén</h3>
                    <p class="text-sm text-gray-500">Es necesario para calcular el stock actual.</p>
                </div>
            @endif

            <x-w-card>
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200"
                            x-text="isEntry ? 'Productos para Ingresar' : 'Productos para Retirar'"></h2>
                    </div>

                    <div class="relative w-full max-w-xs md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar producto..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-2 transition-colors"
                            :class="isEntry ? 'focus:ring-lime-500 focus:border-lime-500' :
                                'focus:ring-rose-500 focus:border-rose-500'"
                            {{ !$warehouse_id ? 'disabled' : '' }}>
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            $hasWarehouse = !empty($warehouse_id);
                            // Calculamos variables PHP para usar en directivas Blade
                            $isExitType = $type == 2;
                            $isDisabled = $isExitType && $stock <= 0;
                        @endphp

                        <div @if (!$isDisabled) wire:click="addFromCard({{ $item->id }})" @endif
                            wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm hover:shadow-md transition-all active:scale-95 flex flex-col justify-between h-full"
                            :class="{
                                'cursor-pointer': !{{ $isDisabled ? 'true' : 'false' }},
                                'cursor-not-allowed opacity-60': {{ $isDisabled ? 'true' : 'false' }},
                                'border-lime-200 hover:border-lime-500': isEntry,
                                'border-rose-200 hover:border-rose-500': !isEntry && !
                                    {{ $isDisabled ? 'true' : 'false' }},
                                'border-gray-200': !isEntry && {{ $isDisabled ? 'true' : 'false' }}
                            }">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded font-mono truncate">
                                        {{ $item->sku ?? '---' }}
                                    </span>

                                    <span class="text-[10px] font-bold flex items-center gap-1 px-1.5 py-0.5 rounded"
                                        :class="{
                                            'text-lime-700 bg-lime-50': isEntry,
                                            'text-rose-700 bg-rose-50': !isEntry && {{ $stock > 0 ? 'true' : 'false' }},
                                            'text-gray-500 bg-gray-100': !isEntry &&
                                                {{ $stock <= 0 ? 'true' : 'false' }}
                                        }">
                                        Stock: {{ $stock }}
                                    </span>
                                </div>

                                <h3
                                    class="text-sm font-semibold text-gray-700 dark:text-gray-200 line-clamp-2 leading-tight">
                                    {{ $item->name }}
                                </h3>

                                <p class="text-[10px] text-gray-400 mt-1">
                                    Costo: ${{ number_format($item->cost ?? 0, 2) }}
                                </p>
                            </div>

                            @if ($isDisabled)
                                <div class="absolute inset-0 flex items-center justify-center z-10">
                                    <span
                                        class="bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded border border-gray-300 shadow-sm transform -rotate-12">
                                        SIN STOCK
                                    </span>
                                </div>
                            @endif

                            @if (!$isDisabled)
                                <div class="mt-2 flex justify-end">
                                    <span class="p-1 rounded-full text-white transition-colors"
                                        :class="isEntry ?
                                            'bg-lime-100 text-lime-600 group-hover:bg-lime-600 group-hover:text-white' :
                                            'bg-rose-100 text-rose-600 group-hover:bg-rose-600 group-hover:text-white'">
                                        <template x-if="isEntry">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </template>
                                        <template x-if="!isEntry">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 12H4"></path>
                                            </svg>
                                        </template>
                                    </span>
                                </div>
                            @endif
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

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-t-4 transition-colors duration-300"
                :class="isEntry ? 'border-lime-500' : 'border-rose-500'">

                <div
                    class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 rounded-t-xl">
                    <h2 class="text-lg font-bold flex items-center gap-2"
                        :class="isEntry ? 'text-lime-700' : 'text-rose-700'">
                        <template x-if="isEntry">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        </template>
                        <template x-if="!isEntry">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        </template>
                        <span x-text="isEntry ? 'Registrar Ingreso' : 'Registrar Salida'"></span>
                    </h2>
                    <span class="text-xs font-mono text-gray-400"
                        x-text="'{{ $serie }}-' + '{{ $correlative }}'"></span>
                </div>

                <div class="p-4">
                    <form wire:submit.prevent="save" class="space-y-4">

                        <div class="grid grid-cols-2 gap-3">
                            <div class="col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1">Tipo de
                                    Movimiento</label>
                                <div class="grid grid-cols-2 gap-2 bg-gray-100 dark:bg-gray-900 p-1 rounded-lg">
                                    <button type="button" @click="$wire.set('type', 1)"
                                        class="px-4 py-2 text-sm font-bold rounded-md transition-all text-center flex items-center justify-center gap-2"
                                        :class="isEntry ? 'bg-white text-lime-600 shadow-sm' :
                                            'text-gray-500 hover:text-gray-700'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Ingreso
                                    </button>
                                    <button type="button" @click="$wire.set('type', 2)"
                                        class="px-4 py-2 text-sm font-bold rounded-md transition-all text-center flex items-center justify-center gap-2"
                                        :class="!isEntry ? 'bg-white text-rose-600 shadow-sm' :
                                            'text-gray-500 hover:text-gray-700'">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 12H4"></path>
                                        </svg>
                                        Salida
                                    </button>
                                </div>
                            </div>

                            <x-w-select label="Almacén *" wire:model.live="warehouse_id" :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']"
                                option-label="name" option-value="id" :disabled="count($products) > 0" />

                            <x-w-select label="Razón" placeholder="..." wire:model="reason_id" :async-data="[
                                'api' => route('api.reasons.index'),
                                'method' => 'POST',
                                'params' => ['type' => $type],
                            ]"
                                option-label="name" option-value="id" />

                            <x-w-input type="date" wire:model="date" label="Fecha" class="col-span-2" />
                        </div>

                        <div class="border rounded-lg overflow-hidden flex flex-col h-[350px] transition-colors"
                            :class="isEntry ? 'bg-gray-100 dark:bg-gray-800 border-lime-500' : 'bg-gray-100 dark:bg-gray-800 border-red-500'">

                            <div class="grid grid-cols-12 gap-2 p-2 text-xs font-bold uppercase border-b"
                                :class="isEntry ? 'bg-lime-50 text-lime-800 border-lime-100' :
                                    'bg-rose-50 text-rose-800 border-rose-100'">
                                <div class="col-span-5">Producto</div>
                                <div class="col-span-2 text-center">Cant.</div>
                                <div class="col-span-3 text-right">Costo</div>
                                <div class="col-span-2"></div>
                            </div>

                            <div class="overflow-y-auto flex-1 p-2 space-y-2">
                                <template x-for="(product, index) in products" :key="index">
                                    <div
                                        class="grid grid-cols-12 gap-2 items-center bg-white dark:bg-gray-800 p-2 rounded shadow-sm border border-gray-100 dark:border-gray-700">

                                        <div class="col-span-5">
                                            <div class="text-xs font-bold text-gray-800 dark:text-gray-200 leading-tight"
                                                x-text="product.name"></div>
                                            <div class="text-[9px] text-gray-400" x-text="product.sku"></div>
                                            <template x-if="!isEntry">
                                                <div class="text-[9px] text-rose-500 font-bold">
                                                    Max: <span x-text="product.stock_actual"></span>
                                                </div>
                                            </template>
                                        </div>

                                        <div class="col-span-2">
                                            <input type="number" x-model.number="product.quantity" min="1"
                                                x-bind:max="!isEntry ? product.stock_actual : ''"
                                                class="w-full h-8 text-center text-sm border-gray-300 rounded focus:ring-2 p-1"
                                                x-bind:class="isEntry ? 'focus:ring-lime-500 bg-gray-100 dark:bg-gray-800' : 'focus:ring-rose-500 bg-gray-100 dark:bg-gray-800'">
                                        </div>

                                        <div class="col-span-3 text-right">
                                            <div class="text-sm font-mono text-gray-600 dark:text-gray-300"
                                                x-text="'$' + Number(product.price).toFixed(2)"></div>
                                            <div class="text-[9px] text-gray-400">
                                                Sub: $<span
                                                    x-text="((product.quantity || 0) * (product.price || 0)).toFixed(2)"></span>
                                            </div>
                                        </div>

                                        <div class="col-span-2 text-center">
                                            <button type="button" @click="removeProduct(index)"
                                                class="text-gray-400 hover:text-red-500 transition-colors">
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
                                        <span class="text-xs">Sin items</span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl space-y-3 border transition-colors"
                            :class="isEntry ? 'bg-gray-100 dark:bg-gray-800 border-lime-500' : 'bg-gray-100 dark:bg-gray-800 border-red-500'">

                            <x-w-textarea label="Observaciones" wire:model="observation" placeholder="..."
                                rows="1" />

                            <div class="flex justify-between items-end pt-2 border-t"
                                :class="isEntry ? 'border-lime-200' : 'border-rose-200'">
                                <span class="text-xs font-bold uppercase"
                                    :class="isEntry ? 'text-lime-800' : 'text-rose-800'">Costo Total</span>
                                <span class="text-3xl font-black"
                                    :class="isEntry ? 'text-lime-600' : 'text-rose-600'">
                                    $<span x-text="total.toFixed(2)"></span>
                                </span>
                            </div>
                        </div>

                        <x-w-button type="submit" spinner="save" xl
                            class="w-full shadow-md text-white transition-colors"
                            x-bind:class="isEntry ? 'bg-lime-600 hover:bg-lime-700' : 'bg-rose-600 hover:bg-rose-700'">
                            <span x-text="isEntry ? 'CONFIRMAR INGRESO' : 'CONFIRMAR SALIDA'"></span>
                        </x-w-button>

                        <x-input-error for="products" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
