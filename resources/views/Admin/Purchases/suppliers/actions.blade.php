<div class="flex items-center space-x-2">
    <x-w-button href="{{ route('admin.suppliers.edit', $supplier) }}" blue xs>Editar</x-w-button>
    <form action="{{ route('admin.suppliers.destroy', $supplier) }}" class="delete-form" method="post">

        @csrf
        @method('DELETE')

        <x-w-button type="submit" red xs>Eliminar</x-w-button>
    </form>
</div>
