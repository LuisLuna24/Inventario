<div>
    <x-w-card>
        <x-w-button blue wire:click="downloadTemplate">
            {!! file_get_contents(public_path('/svg/file-type-xml.svg')) !!}
            Descargar Plantilla
        </x-w-button>
        <p class="mt-3 text-gray-500">Para importar productos, descarga la plantilla y completa los campos requeridos y súbelos aquí.</p>
    </x-w-card>
</div>
