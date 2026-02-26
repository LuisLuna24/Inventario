<div class="flex items-center space-x-2">
    @can('edit-customers')
        <x-w-button href="{{ route('admin.customers.edit', $customer) }}" blue xs>Editar</x-w-button>
    @endcan

    @can('delete-customers')
        <form action="{{ route('admin.customers.destroy', $customer) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
