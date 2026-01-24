<x-admin-layout title="Nuevo proveedor | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'proveedores',
        'href' => route('admin.suppliers.index'),
    ],
    [
        'name' => 'Nuevo proveedor',
    ],
]">

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Nuevo Proveedor') }}
            </h1>
        </div>
    </div>

    <x-w-card>
        <form class="space-y-4" method="POST" action="{{ route('admin.suppliers.store') }}">
            @csrf

            <x-w-input label="Razón Social" name="name" placeholder="Razón social del proveedor"
                value="{{ old('name') }}" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-native-select label="Tipo Doc" name="identity_id">
                    @foreach ($identities as $item)
                        <option value="{{ $item->id }}" @selected(old('identity_id') == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </x-w-native-select>

                <x-w-input label="Numéro del documento" name="document_number"
                    placeholder="Numéro del documento del proveedor" value="{{ old('document_number') }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-input type="email" label="Correo" name="email" placeholder="Correo del proveedor"
                    value="{{ old(key: 'email') }}" />

                <x-w-input label="Teléfono" name="phone" placeholder="Teléfono del proveedor"
                    value="{{ old('phone') }}" />
            </div>

            <x-w-textarea label="Dirección" name="address" placeholder="Dirección del proveedor">
                {{ old('address') }}
            </x-w-textarea>

            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>
</x-admin-layout>
