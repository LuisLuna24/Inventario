<x-admin-layout title="Ordenes de compra | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Ordenes de compra',
    ],
]">
    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Ordenes de compra') }}
            </h1>
        </div>

        <div class="my-2 flex md:ml-4 md:mt-0">
            @can('create-purchase-orders')
            <x-w-button href="{{ route('admin.purchase_orders.create') }}" blue icon="plus" label="Nuevo"
                class="shadow-sm hover:shadow-md transition-all duration-200" />
            @endcan
        </div>
    </div>

    @livewire('admin.datatables.purchases.purcharse-order-table')

    @push('js')
        <script>
            // Usamos const para declarar la variable
            const forms = document.querySelectorAll('.delete-form');

            forms.forEach(form => {
                // Corregido: addEventListener (sobraba una 'r' antes de 'ner')
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: "¿Estás seguro?",
                        text: "¡No podrás revertir esta acción!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Si eliminar",
                        cancelButtonText: "Cancelar"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
