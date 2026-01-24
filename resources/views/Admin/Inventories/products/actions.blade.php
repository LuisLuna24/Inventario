<div class="flex items-center space-x-2">
    <x-w-button href="{{ route('admin.products.kardex', $product) }}" green xs>{!! file_get_contents(public_path('svg/stack-2.svg')) !!}</x-w-button>

    <x-w-button href="{{ route('admin.products.edit', $product) }}" blue xs>{!! file_get_contents(public_path('svg/edit.svg')) !!}</x-w-button>
    <form action="{{ route('admin.products.destroy', $product) }}" class="delete-form" method="post">

        @csrf
        @method('DELETE')

        <x-w-button type="submit" red xs>{!! file_get_contents(public_path('svg/trash.svg')) !!}</x-w-button>
    </form>
</div>
