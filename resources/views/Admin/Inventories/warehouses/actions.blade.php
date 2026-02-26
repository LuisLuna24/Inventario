<div class="flex items-center space-x-2">
    @can('edit-warehouses')
        <x-w-button href="{{ route('admin.warehouses.edit', $warehouse) }}" blue xs>Editar</x-w-button>
    @endcan

    @can('delete-warehouses')
        <form action="{{ route('admin.warehouses.destroy', $warehouse) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
