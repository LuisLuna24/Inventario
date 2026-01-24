@php

    $existingFiles = $product->images->map(function ($image) {
        return [
            'id' => $image->id,
            'name' => basename($image->path),
            'size' => $image->size,
            'url' => Storage::url($image->path),
        ];
    });
@endphp
<x-admin-layout title="Editar Producto | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Productos',
        'href' => route('admin.products.index'),
    ],
    [
        'name' => 'Editar Producto',
    ],
]">
    @push('css')
        <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    @endpush

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Editar Producto') }}
            </h1>
        </div>
    </div>

    <div class="mb-4">
        <form action="{{ route('admin.products.dropzone', $product) }}"
            class="dropzone border-2 border-dashed rounded-xl transition-colors cursor-pointer
                 bg-gray-50 border-gray-300 hover:bg-gray-100
                 dark:bg-secondary-800 dark:border-secondary-600 dark:hover:bg-secondary-700"
            id="my-dropzone" method="POST">
            @csrf
            <div class="dz-message needsclick">
                <div class="flex flex-col items-center justify-center space-y-3">
                    <div class="p-3 bg-white dark:bg-secondary-700 rounded-full shadow-sm">
                        <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                            </path>
                        </svg>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">Suelte las imágenes aquí</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">o haga clic para buscar en su equipo</p>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <x-w-card>
        <form class="space-y-8" method="POST" action="{{ route('admin.products.update', $product) }}">
            @csrf
            @method('PUT')

            <section class="space-y-4">
                <div class="flex items-center justify-between border-b pb-2">
                    <h3 class="text-lg font-medium">Información General</h3>
                    <span class="text-sm text-gray-500 italic">ID del Producto: {{ $product->id }}</span>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <x-w-input label="Nombre" name="name" placeholder="Nombre del producto"
                        value="{{ old('name', $product->name) }}" />

                    <x-w-textarea label="Descripción" name="description" placeholder="Descripción del producto">
                        {{ old('description', $product->description) }}
                    </x-w-textarea>
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2">Identificación e Inventario</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-w-input label="Sku" name="sku" placeholder="Sku del producto"
                        value="{{ old('sku', $product->sku) }}" />

                    <x-w-input type="number" label="Barcode" name="barcode" placeholder="Barcode del producto"
                        value="{{ old('barcode', $product->barcode) }}" />
                </div>
            </section>

            <section class="space-y-4">
                <h3 class="text-lg font-medium border-b pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Finanzas y Clasificación
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <x-w-input type="number" label="Costo" name="cost" prefix="$" placeholder="0.00"
                        value="{{ old('cost', $product->cost) }}" step="0.01" />

                    <x-w-input type="number" label="Precio" name="price" prefix="$" placeholder="0.00"
                        value="{{ old('price', $product->price) }}" step="0.01" />

                    <x-w-native-select label="Categoría" name="category_id">
                        <option value="">Seleccione...</option>
                        @foreach ($categories as $item)
                            <option value="{{ $item->id }}" @selected(old('category_id', $product->category_id) == $item->id)>
                                {{ $item->name }}
                            </option>
                        @endforeach
                    </x-w-native-select>

                    <x-w-select label="Proveedor"
                        placeholder="{{ $product->supplier_id ? $product->supplier->name : 'Seleccione un proveedero' }}"
                        name="supplier_id" :async-data="['api' => route('api.suppliers.index'), 'method' => 'POST']" option-label="name" option-value="id"
                        value="{{ old('supplier_id', $product->supplier_id) == $item->id }}" />
                </div>
            </section>

            <div class="flex justify-end pt-4 gap-x-3">
                <x-w-button href="{{ route('admin.products.index') }}" secondary flat>Cancelar</x-w-button>
                <x-w-button type="submit" blue spinner="save">Actualizar Producto</x-w-button>
            </div>
        </form>
    </x-w-card>

    @push('js')
        <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>

        <script>
            Dropzone.options.myDropzone = {

                addRemoveLinks: true,

                init: function() {
                    let myDropzone = this;

                    // Aquí solo pasamos la variable que ya preparamos arriba
                    let files = @json($existingFiles);

                    files.forEach(function(file) {
                        let mockFile = {
                            id: file.id,
                            name: file.name,
                            size: file.size
                        };

                        myDropzone.emit("addedfile", mockFile);
                        myDropzone.emit("thumbnail", mockFile, file.url);
                        myDropzone.emit("complete", mockFile);
                        myDropzone.files.push(mockFile);
                    });

                    this.on("success", function(file, response) {
                        file.id = response.id;
                    });

                    this.on("removedfile", function(file) {
                        // 1. Verificamos que el archivo tenga un ID (para evitar errores con archivos corruptos o en proceso de carga)
                        if (file.id) {
                            axios.delete(`/admin/images/${file.id}`)

                        }
                    });
                }
            };
        </script>
    @endpush
</x-admin-layout>
