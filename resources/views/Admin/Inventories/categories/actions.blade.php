<div class="flex items-center space-x-2">
    @can('edit-categories')
        <x-w-button href="{{ route('admin.categories.edit', $category) }}" blue xs>Editar</x-w-button>
    @endcan
    @can('delete-categories')
        <form action="{{ route('admin.categories.destroy', $category) }}" class="delete-form" method="post">

            @csrf
            @method('DELETE')

            <x-w-button type="submit" red xs>Eliminar</x-w-button>
        </form>
    @endcan
</div>
