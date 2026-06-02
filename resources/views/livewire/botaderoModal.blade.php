<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm z-50 animate-fade-in-down p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-emap-blue">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-weight-scale mr-2 text-emap-blue"></i> Registro de Pesaje</h2>
            <button wire:click="cerrarModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <form wire:submit.prevent="guardarDescarga" class="p-6">
            
            <div class="bg-blue-50 rounded-lg p-3 mb-5 border border-blue-100 flex items-center justify-between">
                <div>
                    <p class="text-xs text-emap-blue font-bold uppercase">Camión</p>
                    <p class="text-lg font-black text-gray-800">{{ $camion_placa }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-emap-blue font-bold uppercase">Proviene de</p>
                    <p class="text-sm font-bold text-gray-800">{{ $ruta_nombre }}</p>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-black text-gray-700 uppercase mb-2">Peso medido en báscula (Toneladas) *</label>
                <div class="relative">
                    <input type="number" step="any" min="0.1" wire:model="peso_descargado" 
                        class="w-full pl-4 pr-12 py-3 border-2 border-gray-300 rounded-xl focus:ring-0 focus:border-emap-blue text-xl font-bold text-center transition-colors">
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <span class="text-gray-500 font-bold">Ton</span>
                    </div>
                </div>
                @error('peso_descargado') <span class="text-red-500 text-xs font-bold mt-2 block text-center bg-red-50 p-2 rounded">{{ $message }}</span> @enderror
                
                <p class="text-[10px] text-gray-400 text-center mt-2">
                    <i class="fa-solid fa-circle-info"></i> Sugerencia basada en la capacidad máxima del vehículo ({{ $capacidad_maxima }}T). Puedes modificarlo.
                </p>
            </div>

            <div class="mb-8 space-y-3">
                <label class="block text-xs font-black text-gray-700 uppercase mb-2">¿Qué hará el camión tras descargar?</label>
                
                <div wire:click="$set('accion_post_descarga', 'retornar')" 
                     class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all {{ $accion_post_descarga === 'retornar' ? 'border-emap-blue bg-blue-50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $accion_post_descarga === 'retornar' ? 'border-emap-blue' : 'border-gray-300' }}">
                        @if($accion_post_descarga === 'retornar') <div class="w-2.5 h-2.5 bg-emap-blue rounded-full"></div> @endif
                    </div>
                    <div class="ml-3">
                        <p class="font-bold text-gray-800 text-sm">Retornar a Ruta</p>
                        <p class="text-xs text-gray-500">No ha terminado de limpiar su zona.</p>
                    </div>
                </div>

                <div wire:click="$set('accion_post_descarga', 'finalizar')" 
                     class="flex items-center p-3 border-2 rounded-xl cursor-pointer transition-all {{ $accion_post_descarga === 'finalizar' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:bg-gray-50' }}">
                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center {{ $accion_post_descarga === 'finalizar' ? 'border-green-500' : 'border-gray-300' }}">
                        @if($accion_post_descarga === 'finalizar') <div class="w-2.5 h-2.5 bg-green-500 rounded-full"></div> @endif
                    </div>
                    <div class="ml-3">
                        <p class="font-bold text-gray-800 text-sm">Finalizar Turno</p>
                        <p class="text-xs text-gray-500">Cierra el viaje. El camión queda libre.</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" wire:click="cerrarModal" class="px-5 py-3 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-lg">Cancelar</button>
                <button type="submit" wire:loading.attr="disabled" wire:target="guardarDescarga" class="px-5 py-3 text-sm font-bold text-white bg-emap-blue hover:bg-blue-900 rounded-lg shadow-md flex items-center gap-2 transition-all disabled:opacity-50">
                    <span wire:loading.remove wire:target="guardarDescarga"><i class="fa-solid fa-check"></i> Registrar Descarga</span>
                    <span wire:loading wire:target="guardarDescarga"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                </button>
            </div>
        </form>
    </div>
</div>