<div x-data="{
    products: @entangle('products').live,
    total: @entangle('total'),
    removeProduct(index) {
        this.products.splice(index, 1)
    },
    init() {
        // Un solo watcher para recalcular todo
        this.$watch('products', (newProducts) => {
            let total = 0;
            newProducts.forEach(product => {
                total += (parseFloat(product.quantity) || 0) * (parseFloat(product.price) || 0);
            });
            this.total = total;
        });
    }
}" class="max-w-[1800px] mx-auto p-2">

    <div class="flex flex-col lg:flex-row gap-6">

        <div class="lg:w-3/5 space-y-4">
            <x-w-card>
                <div class="flex justify-between items-center mb-4 border-b pb-2 dark:border-gray-700">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-gray-200">Agregar Productos</h2>
                    </div>

                    <div class="relative w-full max-w-xs md:max-w-md">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" type="text"
                            placeholder="Buscar para agregar..."
                            class="pl-10 w-full border-gray-300 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-200 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div
                    class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-3 h-[600px] overflow-y-auto pr-2 custom-scrollbar">
                    @forelse($catalog as $item)
                        @php
                            $stock = intval($item->stock ?? 0);
                            // Verificamos si ya está en la lista de productos actual
                            $inOrder = collect($products)->contains('id', $item->id);
                        @endphp

                        <div @if (!$inOrder) wire:click="addFromCard({{ $item->id }})" @endif
                            wire:key="prod-{{ $item->id }}"
                            class="group relative bg-white dark:bg-gray-800 border rounded-xl p-3 shadow-sm transition-all active:scale-95 flex flex-col justify-between h-full
                            {{ $inOrder ? 'border-emerald-500 ring-1 ring-emerald-500 bg-emerald-50/10 cursor-default' : 'border-gray-200 hover:border-emerald-400 hover:shadow-md cursor-pointer' }}">
                            <div>
                                <div class="flex justify-between items-start mb-2">
                                    <span
                                        class="text-[10px] bg-gray-100 dark:bg-gray-700 text-gray-500 px-1.5 py-0.5 rounded font-mono truncate">
                                        {{ $item->sku ?? '---' }}
                                    </span>

                                    @if ($inOrder)
                                        <span
                                            class="text-[9px] font-bold text-white bg-emerald-500 px-1.5 py-0.5 rounded flex items-center gap-1 shadow-sm">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            EN ORDEN
                                        </span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-500">
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

                            @if (!$inOrder)
                                <div class="mt-2 flex justify-end">
                                    <span
                                        class="p-1 rounded-full text-emerald-600 bg-emerald-50 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
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
            </x-w-card>
        </div>

        <div class="lg:w-2/5 space-y-4">

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border-t-4 border-emerald-500">
                <div
                    class="p-4 border-b dark:border-gray-700 flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 rounded-t-xl">
                    <div>
                        <h2 class="text-lg font-bold text-emerald-700 dark:text-emerald-400 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Editando Orden
                        </h2>
                    </div>
                    <span
                        class="text-sm font-mono font-bold text-gray-600 dark:text-gray-300 bg-gray-200 px-2 py-1 rounded">
                        {{ $serie }}-{{ $correlative }}
                    </span>
                </div>

                <div class="p-4">
                    <form wire:submit.prevent="save" class="space-y-4">

                        <div
                            class="bg-emerald-50/50 dark:bg-gray-900 p-3 rounded-lg space-y-3 border border-emerald-100 dark:border-gray-700">
                            <x-w-select label="Proveedor" wire:model="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']" option-label="name"
                                option-value="id" :clearable="false" />

                            <div class="grid grid-cols-2 gap-3">
                                <x-w-select label="Almacén Destino" wire:model="warehouse_id" :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']"
                                    option-label="name" option-value="id" :clearable="false" />
                                <x-w-input type="date" wire:model="date" label="Fecha" />
                            </div>
                        </div>

                        <div
                            class="border border-emerald-100 dark:border-gray-700 rounded-lg overflow-hidden flex flex-col h-[350px] bg-white dark:bg-gray-800">
                            <div
                                class="grid grid-cols-12 gap-2 p-2 text-xs font-bold uppercase border-b bg-emerald-50 dark:bg-emerald-900/20 text-emerald-800 dark:text-emerald-300">
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
                                        </div>

                                        <div class="col-span-2">
                                            <x-w-input type="number" x-model.number="product.quantity" min="1"/>
                                        </div>

                                        <div class="col-span-3 text-right">
                                            <input type="number" x-model.number="product.price" step="0.01"
                                                class="w-full h-8 text-right text-sm border-gray-300 rounded focus:ring-emerald-500 p-1 font-mono">
                                            <div class="text-[9px] text-gray-400 mt-0.5">
                                                Tot: $<span
                                                    x-text="((parseFloat(product.quantity) || 0) * (parseFloat(product.price) || 0)).toFixed(2)"></span>
                                            </div>
                                        </div>

                                        <div class="col-span-2 text-center">
                                            <button type="button" @click="removeProduct(index)"
                                                class="text-red-400 hover:text-red-600 hover:bg-red-50 p-1 rounded transition-colors">
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
                            </div>
                        </div>

                        <div
                            class="p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl space-y-3 border border-emerald-100 dark:border-emerald-800">
                            <x-w-native-select label="Tipo Comp." wire:model="voucher_type">
                                <option value="1">Factura</option>
                                <option value="2">Nota</option>
                            </x-w-native-select>
                            <x-w-textarea label="Notas" wire:model="observation" placeholder="..."
                                rows="1" />

                            <div
                                class="flex justify-between items-end border-t border-emerald-200 dark:border-emerald-700 pt-3">
                                <span class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase">Total
                                    Orden</span>
                                <span class="text-3xl font-black text-emerald-700 dark:text-emerald-400">
                                    $<span x-text="total.toFixed(2)"></span>
                                </span>
                            </div>
                        </div>

                        <x-w-button type="submit" spinner="save" primary xl
                            class="w-full shadow-md !bg-emerald-600 hover:!bg-emerald-700">
                            GUARDAR CAMBIOS
                        </x-w-button>

                        <x-input-error for="products" />
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
