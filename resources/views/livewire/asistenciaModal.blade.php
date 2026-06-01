<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm z-50 animate-fade-in-down p-4">
    <div class="max-w-xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-green-500">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-clipboard-user mr-2 text-green-500"></i> Despacho y Asistencia</h2>
                <p class="text-xs text-gray-500 mt-1">Ruta: {{ $datos_asignacion->ruta->nombre_ruta ?? 'N/A' }}</p>
            </div>
            <button wire:click="closeAsistenciaModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        @php
            // Verificamos si la ruta ya terminó para bloquear los controles
            $esSoloLectura = $datos_asignacion->estado_operacion === 'Finalizada';
        @endphp

        <form wire:submit.prevent="guardarAsistencia" class="p-6">
            <p class="text-sm font-bold text-gray-600 uppercase mb-4 border-b pb-2">
                {{ $esSoloLectura ? 'Personal que asistió (Modo Lectura):' : 'Marcar personal presente:' }}
            </p>
            
            <div class="space-y-3 mb-6 max-h-[300px] overflow-y-auto pr-2">
                @foreach($datos_asignacion->detallesCuadrilla as $detalle)
                    <label class="flex items-center p-3 border rounded-lg transition-all {{ $asistencias[$detalle->id_usuario] ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white' }} {{ $esSoloLectura ? 'cursor-default opacity-80' : 'cursor-pointer hover:bg-green-50' }}">
                        
                        <input type="checkbox" wire:model="asistencias.{{ $detalle->id_usuario }}" 
                            class="w-5 h-5 text-green-600 border-gray-300 rounded {{ $esSoloLectura ? 'cursor-not-allowed' : 'focus:ring-green-500' }}"
                            {{ $esSoloLectura ? 'disabled' : '' }}>
                        
                        <div class="ml-4 flex-1">
                            <p class="text-sm font-bold text-gray-900">{{ $detalle->usuario->nombre_completo }}</p>
                            <p class="text-xs text-gray-500">{{ $detalle->rol_en_viaje }}</p>
                        </div>
                        @if($detalle->hora_marcaje)
                            <span class="text-[10px] text-green-600 font-bold bg-green-100 px-2 py-1 rounded">
                                <i class="fa-regular fa-clock"></i> {{ \Carbon\Carbon::parse($detalle->hora_marcaje)->format('H:i') }}
                            </span>
                        @endif
                    </label>
                @endforeach
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" wire:click="closeAsistenciaModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg">
                    {{ $esSoloLectura ? 'Cerrar Detalles' : 'Cancelar' }}
                </button>
                
                @if(!$esSoloLectura)
                    <button type="submit" wire:loading.attr="disabled" wire:target="guardarAsistencia" class="px-5 py-2.5 text-sm font-bold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow flex items-center gap-2">
                        <span wire:loading.remove wire:target="guardarAsistencia"><i class="fa-solid fa-check-double"></i> Confirmar Asistencia y Despachar</span>
                        <span wire:loading wire:target="guardarAsistencia"><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>