<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 animate-fade-in-down">
    <div class="max-w-2xl w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800">
                <i class="fa-solid fa-truck-medical mr-2 text-orange-500"></i>
                {{ $camion_id ? 'Editar Vehículo' : 'Registrar Nuevo Vehículo' }}
            </h2>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <form wire:submit.prevent="enviarClick" class="p-6" autocomplete="off">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">N° de Placa *</label>
                    <input wire:model="placa" type="text" autocomplete="off" placeholder="Ej: 1234-ABC" style="text-transform:uppercase"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow font-mono text-lg tracking-wider">
                    @error('placa') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Modelo / Marca</label>
                    <input wire:model="modelo" type="text" autocomplete="off" placeholder="Ej: Volvo FMX 2021"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                    @error('modelo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Capacidad (Toneladas) *</label>
                    <div class="relative">
                        <input wire:model="capacidad_ton" type="number" step="0.1" autocomplete="off" placeholder="0.0"
                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-400 text-sm font-bold">
                            Ton.
                        </div>
                    </div>
                    @error('capacidad_ton') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tipo de Vehículo *</label>
                    <select wire:model="dimension_tipo" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow bg-white">
                        <option value="">Seleccione el tipo...</option>
                        <option value="Compactador Grande">Compactador Grande</option>
                        <option value="Compactador Mediano">Compactador Mediano</option>
                        <option value="Volqueta">Volqueta Abierta</option>
                        <option value="Camioneta">Camioneta de Apoyo</option>
                    </select>
                    @error('dimension_tipo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="hidden md:col-span-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Estado Operativo Actual *</label>
                    <select wire:model="estado_operativo" 
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow bg-white font-bold">
                        <option value="Operativo">🟢 Activo y Operativo</option>
                        <option value="En Mantenimiento">🟡 En Taller / Mantenimiento</option>
                        <option value="Fuera de Servicio">🔴 De Baja / Fuera de Servicio</option>
                    </select>
                    @error('estado_operativo') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
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
                        <i class="fa-solid fa-check"></i> {{ $camion_id ? 'Guardar Cambios' : 'Registrar Vehículo' }}
                    </span>

                    <span wire:loading wire:target="enviarClick" class="flex items-center gap-2">
                        <i class="fa-solid fa-spinner fa-spin"></i> Procesando...
                    </span>
                </button>
            </div>
            
        </form>
    </div>
</div>