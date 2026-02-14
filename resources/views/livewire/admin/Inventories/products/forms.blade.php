<div>
    <x-w-card>
        <form class="space-y-8" wire:submit.prevent="save">
            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Información General</h3>
                <div class="grid grid-cols-1 gap-4">
                    <x-w-input label="Nombre" wire:model.blur="name" placeholder="Nombre del producto" />

                    <x-w-textarea label="Descripción" wire:model.blur="description"
                        placeholder="Descripción del producto" />
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Identificación e Inventario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-w-input label="Sku" wire:model.blur="sku" placeholder="Sku del producto" />

                    <x-w-input type="number" label="Barcode" wire:model.blur="barcode"
                        placeholder="Barcode del producto" />
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Finanzas y Organización</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-w-input type="number" label="Costo" wire:model.live="cost" placeholder="0.00" step="0.01"
                        hint="Lo que te cuesta a ti" />

                    <x-w-input type="number" label="Precio de Venta" wire:model="price" placeholder="0.00"
                        step="0.01" hint="Precio final calculado"/>

                    <x-w-select label="Categoría" placeholder="Seleccione" wire:model.live="category_id"
                        :options="$categories" option-label="name" option-value="id" />

                    <x-w-select label="Proveedor" placeholder="Seleccione" wire:model="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']"
                        option-label="name" option-value="id" />
                </div>
            </section>

            <div class="flex justify-end pt-4">
                <x-w-button type="submit" blue spinner="save" class="w-full md:w-auto">
                    Guardar Producto
                </x-w-button>
            </div>
        </form>
    </x-w-card>
</div>
