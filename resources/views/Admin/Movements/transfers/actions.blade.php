<div class="flex items-center space-x-2">
    <x-w-button green>
        {!! file_get_contents(public_path('svg/mail.svg')) !!}
    </x-w-button>
    <x-w-button blue href="{{ route('admin.transfers.pdf', $transfer) }}">
        {!! file_get_contents(public_path('svg/file-type-pdf.svg')) !!}
    </x-w-button>
</div>
