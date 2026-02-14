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
    @livewire('admin.inventories.products.forms',['product' => $product])

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
