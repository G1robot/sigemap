<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 animate-fade-in-down">
    <div class="max-w-lg w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-orange-500">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-map mr-2 text-orange-500"></i>
                {{ $zona_id ? 'Editar Zona' : 'Registrar Nueva Zona' }}
            </h2>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <form wire:submit.prevent="enviarClick" class="p-6" autocomplete="off">
            
            <div class="space-y-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre de la Zona *</label>
                    <input wire:model="nombre_zona" type="text" autocomplete="off" placeholder="Ej: Zona Alta, Casco Viejo, Plan 40..."
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow text-base">
                    @error('nombre_zona') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Descripción / Referencias (Opcional)</label>
                    <textarea wire:model="descripcion" rows="3" placeholder="Ej: Abarca desde la Av. Murillo hasta la plaza principal..."
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow resize-none"></textarea>
                    @error('descripcion') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors">
                    Cancelar
                </button>
                
                <button type="submit" 
                    wire:loading.attr="disabled" 
                    wire:target="enviarClick"
                    class="px-5 py-2.5 text-sm font-bold text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-colors shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    
                    <span wire:loading.remove wire:target="enviarClick" class="flex items-center gap-2">
                        <i class="fa-solid fa-check"></i> {{ $zona_id ? 'Guardar Cambios' : 'Registrar Zona' }}
                    </span>

                    <span wire:loading wire:target="enviarClick" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Guardando...
                    </span>
                </button>
            </div>
            
        </form>
    </div>
</div>