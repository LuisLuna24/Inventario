<div class="flex items-center space-x-2">
    @can('email-transfers')
        <x-w-button green wire:click="openModal({{ $transfer->id }})">
            {!! file_get_contents(public_path('svg/mail.svg')) !!}
        </x-w-button>
    @endcan
    <x-w-button blue href="{{ route('admin.transfers.pdf', $transfer) }}">
        {!! file_get_contents(public_path('svg/file-type-pdf.svg')) !!}
    </x-w-button>
</div>
