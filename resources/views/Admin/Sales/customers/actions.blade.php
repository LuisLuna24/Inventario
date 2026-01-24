<div class="flex items-center space-x-2">
    <x-w-button href="{{ route('admin.customers.edit', $customer) }}" blue xs>Editar</x-w-button>
    <form action="{{ route('admin.customers.destroy', $customer) }}" class="delete-form" method="post">

        @csrf
        @method('DELETE')

        <x-w-button type="submit" red xs>Eliminar</x-w-button>
    </form>
</div>
