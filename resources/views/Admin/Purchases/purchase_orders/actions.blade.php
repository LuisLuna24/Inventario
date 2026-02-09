<div class="flex items-center space-x-2">
    <x-w-button green xs wire:click="openModal({{ $purchaseOrder->id }})">
        {!! file_get_contents(public_path('svg/mail.svg')) !!}
    </x-w-button>
    <x-w-button blue xs href="{{ route('admin.purchases.pdf', $purchaseOrder) }}">
        {!! file_get_contents(public_path('svg/file-type-pdf.svg')) !!}
    </x-w-button>

    <x-w-button blue href="{{ route('admin.purchase_orders.edit', $purchaseOrder) }}" blue xs>
        {!! file_get_contents(public_path('svg/edit.svg')) !!}
    </x-w-button>
</div>
