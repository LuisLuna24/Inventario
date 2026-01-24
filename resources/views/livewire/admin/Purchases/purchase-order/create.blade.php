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
}">
    <x-w-card title="Registro de Comprobante">
        <form wire:submit.prevent="save" class="space-y-6">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <x-w-native-select label="Tipo de comprobante" wire:model="voucher_type">
                    <option value="1">Factura</option>
                    <option value="2">Nota</option>
                </x-w-native-select>
                <x-w-input wire:model="serie" label="Serie" disabled />
                <x-w-input wire:model="correlative" label="Correlativo" disabled />
                <x-w-input type="date" wire:model="date" label="Fecha" />
                <div class="col-span-1 md:col-span-2">
                    <x-w-select label="Proveedor" placeholder="Seleccione un proveedor" wire:model.live="supplier_id"
                        :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']" option-label="name" option-value="id" />
                </div>
                <div class="col-span-1 md:col-span-2">
                    <x-w-select label="Almacen" placeholder="Seleccione un almacen" wire:model.live="warehouse_id"
                        :async-data="['api' => route('api.warehouses.index'), 'method' => 'POST']" option-label="name" option-value="id" option-description="description"
                        :disabled="count($products)" />
                </div>
            </div>

            <hr class="dark:border-gray-700">

            <div class="flex flex-col sm:flex-row items-end gap-3">
                <div class="w-full">
                    <x-w-select wire:key="select-products-supplier-{{ $supplier_id }}" label="Producto"
                        placeholder="Seleccione un producto" wire:model.live="product_id" :async-data="[
                            'api' => route('api.products.index'),
                            'method' => 'POST',
                            'params' => ['supplier_id' => $supplier_id],
                        ]"
                        option-label="name" option-value="id" :disabled="!$supplier_id" {{-- Bloqueado si no hay proveedor --}} />
                </div>
                <x-w-button wire:click="addProduct" spinner primary class="w-full sm:w-auto whitespace-nowrap">
                    Agregar
                </x-w-button>
            </div>

            <x-input-error for="products" />
            <div class="hidden md:block overflow-hidden border border-gray-200 dark:border-gray-700 rounded-lg">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-3">Producto</th>
                            <th class="px-4 py-3 w-32">Cantidad</th>
                            <th class="px-4 py-3 w-40">Precio</th>
                            <th class="px-4 py-3">Subtotal</th>
                            <th class="px-4 py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(product, index) in products" :key="index">
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                                <td class="px-4 py-2 font-medium text-gray-900 dark:text-white" x-text="product.name">
                                </td>
                                <td class="px-4 py-2">
                                    <x-w-input type="number" x-model.number="product.quantity" min="1" />
                                </td>
                                <td class="px-4 py-2">
                                    <x-w-input type="number" step="0.01" x-model.number="product.price"
                                        prefix="$" />
                                </td>
                                <td class="px-4 py-2 font-semibold">
                                    $<span x-text="((product.quantity || 0) * (product.price || 0)).toFixed(2)"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <x-w-mini-button rounded icon="trash" red x-on:click="removeProduct(index)" />
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="md:hidden space-y-4">
                <template x-for="(product, index) in products" :key="index">
                    <div
                        class="p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 relative">
                        <div class="absolute top-2 right-2">
                            <x-w-mini-button rounded icon="trash" red x-on:click="removeProduct(index)" />
                        </div>
                        <div class="font-bold text-lg mb-3 pr-8" x-text="product.name"></div>
                        <div class="grid grid-cols-2 gap-4">
                            <x-w-input label="Cant." type="number" x-model.number="product.quantity" />
                            <x-w-input label="Precio" type="number" step="0.01" x-model.number="product.price"
                                prefix="$" />
                        </div>
                        <div class="mt-3 text-right font-bold text-primary-600">
                            Subtotal: $<span
                                x-text="((product.quantity || 0) * (product.price || 0)).toFixed(2)"></span>
                        </div>
                    </div>
                </template>
            </div>

            <template x-if="products.length === 0">
                <div
                    class="text-center py-8 text-gray-500 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    No hay productos agregados.
                </div>
            </template>

            <div
                class="flex flex-col-reverse md:flex-row md:items-center gap-4 bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                <div class="flex-1">
                    <x-w-input label="Observaciones" wire:model="observations" placeholder="Notas adicionales..." />
                </div>
                <div class="text-right space-y-1">
                    <p class="text-sm text-gray-500 uppercase">Total a pagar</p>
                    <p class="text-3xl font-bold text-primary-600">
                        $<span x-text="total.toFixed(2)"></span> <span
                            class="text-sm font-normal text-gray-400">MXN</span>
                    </p>
                </div>
            </div>

            <div class="flex justify-end pt-4">
                <x-w-button type="submit" icon="check" spinner="save" primary lg class="w-full sm:w-auto">
                    Guardar
                </x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
