<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 animate-fade-in-down">
    <div class="max-w-md w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-yellow-500">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-wrench mr-2 text-yellow-500"></i>
                Enviar a Mantenimiento
            </h2>
            <button wire:click="cerrarMantenimiento" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <form wire:submit.prevent="guardarMantenimiento" class="p-6">
            
            <p class="text-sm text-gray-600 mb-4">
                El vehículo cambiará su estado a <span class="font-bold text-yellow-600">"En Mantenimiento"</span> y se registrará su ingreso al taller en la fecha actual.
            </p>

            <div>
                <label class="block text-xs font-bold text-gray-600 uppercase mb-2">Motivo / Descripción (Opcional)</label>
                <textarea wire:model="mantenimiento_descripcion" rows="4" placeholder="Ej: Cambio de aceite, falla en el sistema hidráulico..."
                    class="w-full border border-gray-300 rounded-lg p-3 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500 transition-shadow resize-none"></textarea>
            </div>

            <div class="mt-6 flex justify-end gap-3 pt-2">
                <button type="button" wire:click="cerrarMantenimiento" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </button>
                
                <button type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="guardarMantenimiento"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg transition-colors shadow-sm flex items-center gap-2">
                    
                    <span wire:loading.remove wire:target="guardarMantenimiento" class="flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Confirmar Ingreso
                    </span>

                    <span wire:loading wire:target="guardarMantenimiento" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                    </span>
                </button>
            </div>
            
        </form>
    </div>
</div>