<div class="flex items-center space-x-2">
    @can('email-quotes')
        <x-w-button green wire:click="openModal({{ $quote->id }})">
            {!! file_get_contents(public_path('svg/mail.svg')) !!}
        </x-w-button>
    @endcan
    <x-w-button blue href="{{ route('admin.quotes.pdf', $quote) }}">
        {!! file_get_contents(public_path('svg/file-type-pdf.svg')) !!}
    </x-w-button>
</div>
