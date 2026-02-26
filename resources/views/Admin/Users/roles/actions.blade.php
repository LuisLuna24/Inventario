<div class="flex items-center space-x-2">
    @can('edit-roles')
        <x-w-button href="{{ route('admin.roles.edit', $role) }}" blue xs>Editar</x-w-button>
    @endcan

    @can('delete-roles')
        <form action="{{ route('admin.roles.destroy', $role) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
