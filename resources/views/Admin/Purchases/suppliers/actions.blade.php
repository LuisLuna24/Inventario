<div class="flex items-center space-x-2">
    @can('edit-suppliers')
        <x-w-button href="{{ route('admin.suppliers.edit', $supplier) }}" blue xs>Editar</x-w-button>
    @endcan

    @can('delete-suppliers')
        <form action="{{ route('admin.suppliers.destroy', $supplier) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
