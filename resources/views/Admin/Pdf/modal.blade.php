<x-w-modal-card wire:model="form.open" title="Enviar comprobante por Email">
    <div class="flex flex-col items-center mb-6">
        <div class="p-3 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-3 transition-colors">
            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>
        <h3 class="text-xl font-semibold text-gray-800 dark:text-gray-100">Enviar Documento</h3>
    </div>

    <div
        class="bg-gray-50 dark:bg-secondary-900/50 rounded-lg p-4 mb-6 border border-gray-100 dark:border-secondary-700 transition-colors">
        <div class="flex justify-between mb-2">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Documento:</span>
            <span class="text-sm font-bold text-gray-800 dark:text-blue-300"># {{ $form['document'] ?? '001-234' }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest">Proveedor:</span>
            <span
                class="text-sm font-bold text-gray-800 dark:text-gray-200">{{ $form['supplier'] ?? 'Nombre del Proveedor' }}</span>
        </div>
    </div>

    <form wire:submit.prevent="sendEmail" class="space-y-5">
        <div>
            <x-w-input label="Correo electrónico" placeholder="ejemplo@correo.com" wire:model="form.email"
                class="dark:bg-secondary-800 dark:text-white dark:border-secondary-600" />
        </div>

        <div class="flex gap-3 mt-8">
            <x-w-button flat label="Cancelar" x-on:click="close"
                class="w-1/3 dark:text-gray-400 dark:hover:bg-secondary-800" />
            <x-w-button primary spinner="sendEmail" type="submit" label="Enviar correo"
                class="w-2/3 shadow-md dark:shadow-blue-900/20" />
        </div>
    </form>
</x-w-modal-card>
