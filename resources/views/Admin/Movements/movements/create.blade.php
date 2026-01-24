<x-admin-layout title="Nuevo movimiento | Incotizaciónrios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Entradas y salidas',
        'href' => route('admin.quotes.index'),
    ],
    [
        'name' => 'Nuevo movimiento',
    ],
]">

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Nuevo movimiento') }}
            </h1>
        </div>
    </div>


    @livewire('admin.movements.movements.movement-create')

</x-admin-layout>
