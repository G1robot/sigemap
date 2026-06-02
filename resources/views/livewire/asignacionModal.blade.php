<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-40 animate-fade-in-down p-4">
    <div class="max-w-5xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden flex flex-col max-h-full border-t-4 border-emap-blue">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-calendar-plus mr-2 text-emap-blue"></i> Nueva Planificación</h2>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <div class="p-6 overflow-y-auto">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <div class="space-y-4">
                    <h3 class="text-xs font-black text-gray-400 tracking-wider border-b pb-2"><i class="fa-solid fa-truck-fast mr-1"></i> DETALLES DEL VIAJE</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Fecha de Operación *</label>
                        <input type="date" wire:model="fecha" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-emap-blue text-sm transition-shadow">
                        @error('fecha') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-gray-50 p-3 rounded-xl border border-gray-100">
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Ruta a Cubrir *</label>
                        <select wire:model.live="id_ruta" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-white shadow-sm focus:ring-2 focus:ring-emap-blue transition-shadow">
                            <option value="">Seleccione la ruta espacial...</option>
                            @foreach($rutas as $ruta)
                                <option value="{{ $ruta->id_ruta }}">{{ $ruta->nombre_ruta }}</option>
                            @endforeach
                        </select>
                        @error('id_ruta') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror

                        @if($id_ruta)
                            @php $rutaDetalle = $rutas->firstWhere('id_ruta', $id_ruta); @endphp
                            @if($rutaDetalle)
                                <div class="mt-3 bg-blue-50 border border-blue-100 rounded-lg p-3 animate-fade-in-down">
                                    <p class="text-blue-800 text-sm font-black mb-2 flex items-center gap-2">
                                        <i class="fa-solid fa-map-location-dot"></i> Información del Trayecto
                                    </p>
                                    <div class="grid grid-cols-1 gap-2 text-xs text-blue-700">
                                        <p><i class="fa-solid fa-location-crosshairs w-4"></i> <strong>Distrito/Zona:</strong> {{ $rutaDetalle->zona->nombre_zona ?? 'Sin Zona Asignada' }}</p>
                                        <p><i class="fa-regular fa-clock w-4"></i> <strong>Horario Permitido:</strong> <span class="bg-blue-200 text-blue-800 px-2 py-0.5 rounded font-bold">{{ $rutaDetalle->horario_permitido }}</span></p>
                                    </div>
                                </div>
                            @endif
                        @endif
                    </div>

                    <input type="hidden" wire:model="turno">

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Vehículo Asignado *</label>
                        <select wire:model="id_camion" class="w-full border border-gray-300 rounded-lg p-2.5 text-sm bg-white focus:ring-2 focus:ring-emap-blue transition-shadow">
                            <option value="">Seleccione un camión operativo...</option>
                            @foreach($camiones as $camion)
                                <option value="{{ $camion->id_camion }}">Placa: {{ $camion->placa }} ({{ $camion->dimension_tipo }} - {{ $camion->capacidad_ton }}T)</option>
                            @endforeach
                        </select>
                        @error('id_camion') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex flex-col h-full border-l border-gray-100 pl-0 lg:pl-6">
                    <h3 class="text-xs font-black text-gray-400 tracking-wider border-b pb-2 mb-4"><i class="fa-solid fa-users-gear mr-1"></i> ARMADO DE CUADRILLA</h3>
                    
                    <div class="flex gap-2 mb-4">
                        <div class="flex-1">
                            <select wire:model="trabajador_seleccionado" class="w-full border border-gray-300 rounded-lg p-2 text-sm bg-white focus:ring-2 focus:ring-emap-blue transition-shadow">
                                <option value="">Elegir trabajador...</option>
                                @foreach($personal as $p)
                                    <option value="{{ $p->id_usuario }}">{{ $p->nombre_completo }} ({{ $p->cargo_base }})</option>
                                @endforeach
                            </select>
                            @error('trabajador_seleccionado') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>
                        <button wire:click="agregarTrabajador" wire:loading.attr="disabled" wire:target="agregarTrabajador" class="bg-gray-800 hover:bg-gray-900 text-white px-4 rounded-lg font-bold text-sm transition-colors h-10">
                            <span wire:loading.remove wire:target="agregarTrabajador">Añadir</span>
                            <span wire:loading wire:target="agregarTrabajador"><i class="fa-solid fa-spinner fa-spin"></i></span>
                        </button>
                    </div>

                    <div class="flex-1 bg-gray-50 rounded-lg border border-gray-200 p-2 overflow-y-auto min-h-[150px]">
                        @forelse($cuadrilla as $index => $miembro)
                            <div wire:key="miembro-{{ $miembro['id_usuario'] }}" class="flex justify-between items-center bg-white p-2 mb-2 rounded border border-gray-100 shadow-sm">
                                <div>
                                    <p class="text-sm font-bold text-gray-800">{{ $miembro['nombre'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $miembro['rol_en_viaje'] }}</p>
                                </div>
                                <button wire:click="quitarTrabajador({{ $index }})" class="text-gray-400 hover:text-red-500 px-2 transition-colors">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </div>
                        @empty
                            <div class="h-full flex flex-col items-center justify-center text-gray-400 text-xs">Sin personal asignado</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
            <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-100 rounded-lg">Cancelar</button>
            <button wire:click="guardarPlanificacion" wire:loading.attr="disabled" wire:target="guardarPlanificacion" class="px-5 py-2.5 text-sm font-bold text-white bg-emap-blue hover:bg-blue-900 rounded-lg flex items-center gap-2 transition-all shadow-sm disabled:opacity-50">
                <span wire:loading.remove wire:target="guardarPlanificacion"><i class="fa-solid fa-save"></i> Guardar Programación</span>
                <span wire:loading wire:target="guardarPlanificacion"><i class="fa-solid fa-spinner fa-spin"></i> Procesando...</span>
            </button>
        </div>
    </div>
</div>