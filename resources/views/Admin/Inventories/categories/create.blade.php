<x-admin-layout title="Nueva Caegoría | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Caegorías',
        'href' => route('admin.categories.index'),
    ],
    [
        'name' => 'Nueva Caegoría',
    ],
]">

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Nueva Caegoría') }}
            </h1>
        </div>
    </div>


    <x-w-card>
        <form class="space-y-4" method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <x-w-input label="Nombre" name="name" placeholder="Nombre de la categoría" value="{{ old('name') }}" />
            <x-w-textarea label="Descripción" name="description" placeholder="Descripción de la categoría">
                {{ old('description') }}
            </x-w-textarea>
            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>

</x-admin-layout>
