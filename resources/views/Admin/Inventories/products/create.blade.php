<x-admin-layout title="Nuevo producto | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Productos',
        'href' => route('admin.products.index'),
    ],
    [
        'name' => 'Nuevo Producto',
    ],
]">

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Nuevo Producto') }}
            </h1>
        </div>
    </div>


    <x-w-card>
        <form class="space-y-8" method="POST" action="{{ route('admin.products.store') }}">
            @csrf

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Información General</h3>
                <div class="grid grid-cols-1 gap-4">
                    <x-w-input label="Nombre" name="name" placeholder="Nombre del producto"
                        value="{{ old('name') }}" />

                    <x-w-textarea label="Descripción" name="description" placeholder="Descripción del producto">
                        {{ old('description') }}
                    </x-w-textarea>
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Identificación e Inventario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-w-input label="Sku" name="sku" placeholder="Sku del producto"
                        value="{{ old('sku') }}" />

                    <x-w-input type="number" label="Barcode" name="barcode" placeholder="Barcode del producto"
                        value="{{ old('barcode') }}" />
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Finanzas y Organización</h3>
                <div x-data="{
                    cost: {{ old('cost', 0) }},
                    price: {{ old('price', 0) }},
                    categoryId: '{{ old('category_id', '') }}',
                    // Creamos un mapa de ID -> Porcentaje desde PHP
                    categoryPorcents: {{ $categories->pluck('porcent', 'id')->toJson() }},

                    calculate() {
                        let porcent = this.categoryPorcents[this.categoryId] || 0;
                        if (this.cost > 0) {
                            let total = parseFloat(this.cost) + (parseFloat(this.cost) * parseFloat(porcent) / 100);
                            this.price = total.toFixed(2);
                        } else {
                            this.price = 0;
                        }
                    }
                }" x-init="$watch('cost', value => calculate());
                $watch('categoryId', value => calculate())">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <x-w-input type="number" label="Costo" name="cost" x-model="cost" placeholder="0.00"
                            step="0.01" hint="Lo que te cuesta a ti" />

                        <x-w-input type="number" label="Precio de Venta" name="price" x-model="price"
                            placeholder="0.00" step="0.01" hint="Precio final al público" readonly />

                        <x-w-select label="Categoría" placeholder="Seleccione" name="category_id" x-model="categoryId"
                            :options="$categories" option-label="name" option-value="id" x-on:change="calculate()" />

                        <x-w-select label="Proveedor" placeholder="Seleccione" name="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']"
                            option-label="name" option-value="id" />
                    </div>
                </div>
            </section>

            <div class="flex justify-end pt-4">
                <x-w-button type="submit" blue class="w-full md:w-auto">Guardar Producto</x-w-button>
            </div>
        </form>
    </x-w-card>
</x-admin-layout>
