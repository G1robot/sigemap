<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm z-50 p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-red-500">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between">
            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-triangle-exclamation mr-2 text-red-500"></i> Alerta de Contingencia</h2>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500">&times;</button>
        </div>

        <form wire:submit.prevent="guardar" class="p-6">
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Fecha del Evento *</label>
                <input type="date" wire:model="fecha" class="w-full border border-gray-300 rounded-lg p-2.5">
                @error('fecha') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-5">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Motivo (Ej. Bloqueo camino, Paro cívico) *</label>
                <textarea wire:model="descripcion" rows="3" class="w-full border border-gray-300 rounded-lg p-2.5"></textarea>
                @error('descripcion') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="mb-6 p-3 bg-red-50 rounded-lg border border-red-100">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" wire:model="bloqueo_total" class="w-5 h-5 text-red-600 rounded border-gray-300 focus:ring-red-500">
                    <div class="ml-3">
                        <span class="block text-sm font-black text-red-800 uppercase">¿Es Bloqueo Total?</span>
                        <span class="block text-xs text-red-600">Si marcas esto, el sistema bloqueará la programación de camiones para este día.</span>
                    </div>
                </label>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">Cancelar</button>
                
                <button type="submit" wire:loading.attr="disabled" wire:target="guardar" class="px-5 py-2.5 text-sm font-bold text-white bg-red-500 hover:bg-red-600 rounded-lg shadow-sm flex items-center gap-2 transition-all">
                    <span wire:loading.remove wire:target="guardar">Guardar Alerta</span>
                    <span wire:loading wire:target="guardar"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                </button>
            </div>
        </form>
    </div>
</div>