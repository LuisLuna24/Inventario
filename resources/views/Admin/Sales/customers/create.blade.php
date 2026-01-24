<x-admin-layout title="Nuevo cliente | Inventarios" :breadcrumbs="[
    [
        'name' => 'Dashboard',
        'href' => route('admin.dashboard'),
    ],
    [
        'name' => 'Clientes',
        'href' => route('admin.customers.index'),
    ],
    [
        'name' => 'Nuevo Cliente',
    ],
]">

    <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-200 dark:border-gray-800">
        <div class="min-w-0 flex-1">
            <h1
                class="text-2xl font-bold leading-7 text-gray-900 dark:text-white sm:truncate sm:text-3xl sm:tracking-tight">
                {{ __('Nuevo Cliente') }}
            </h1>
        </div>
    </div>

    <x-w-card>
        <form class="space-y-4" method="POST" action="{{ route('admin.customers.store') }}">
            @csrf

            <x-w-input label="Razón Social" name="name" placeholder="Razón social del cliente"
                value="{{ old('name') }}" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-native-select label="Tipo Doc" name="identity_id">
                    @foreach ($identities as $item)
                        <option value="{{ $item->id }}" @selected(old('identity_id') == $item->id)>{{ $item->name }}</option>
                    @endforeach
                </x-w-native-select>

                <x-w-input label="Numéro del documento" name="document_number"
                    placeholder="Numéro del documento del cliente" value="{{ old('document_number') }}" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <x-w-input type="email" label="Correo" name="email" placeholder="Correo del cliente"
                    value="{{ old(key: 'email') }}" />

                <x-w-input label="Teléfono" name="phone" placeholder="Teléfono del cliente"
                    value="{{ old('phone') }}" />
            </div>

            <x-w-textarea label="Dirección" name="address" placeholder="Dirección del cliente">
                {{ old('address') }}
            </x-w-textarea>

            <div class="flex justify-end">
                <x-w-button type="submit" blue>Guardar</x-w-button>
            </div>
        </form>
    </x-w-card>
</x-admin-layout>
