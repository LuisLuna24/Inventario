<div class="flex items-center space-x-2">
    @can('edit-users')
        <x-w-button href="{{ route('admin.users.edit', $user) }}" blue xs>Editar</x-w-button>
    @endcan

    @can('delete-users')
        <form action="{{ route('admin.users.destroy', $user) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
