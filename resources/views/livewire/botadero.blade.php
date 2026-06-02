<div class="px-4 pb-10">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">CONTROL DE BÁSCULA - BOTADERO</h2>
        <p class="text-sm text-gray-500 mt-1">Monitoreo de camiones operando y registro de ingresos de residuos sólidos.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($viajes_activos as $viaje)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md hover:border-blue-200 transition-all relative">
                
                <div class="absolute top-4 right-4 flex items-center gap-1.5">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider">En Operación</span>
                </div>

                <div class="p-6">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-blue-100 text-emap-blue flex items-center justify-center text-2xl shadow-inner border border-blue-200">
                            <i class="fa-solid fa-truck"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-gray-800">Placa: {{ $viaje->camion->placa ?? 'N/A' }}</h3>
                            <p class="text-xs text-gray-500 font-medium bg-gray-100 px-2 py-0.5 rounded-full inline-block mt-1">
                                Capacidad: {{ $viaje->camion->capacidad_ton ?? '0' }} Ton.
                            </p>
                        </div>
                    </div>

                    <div class="space-y-3 border-t border-gray-100 pt-4 mb-5 text-sm">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-map-location-dot text-gray-400 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Ruta Actual</p>
                                <p class="font-bold text-gray-700 leading-tight">{{ $viaje->ruta->nombre_ruta ?? 'Ruta Eliminada' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-steering-wheel text-gray-400 mt-0.5 w-4 text-center"></i>
                            <div>
                                <p class="text-xs text-gray-400 uppercase font-bold">Chofer Asignado</p>
                                <p class="font-medium text-gray-700 leading-tight">
                                    @php
                                        $chofer = $viaje->detallesCuadrilla->first();
                                    @endphp
                                    {{ $chofer ? $chofer->usuario->nombre_completo : 'Chofer no definido' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <button wire:click="abrirPesaje({{ $viaje->id_asignacion }})" 
                        class="w-full bg-gray-800 hover:bg-emap-blue text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-weight-scale"></i> Registrar Descarga
                    </button>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-gray-400">
                <i class="fa-solid fa-mug-hot text-5xl mb-4 text-gray-200"></i>
                <h3 class="text-lg font-bold text-gray-600 mb-1">Sin movimiento</h3>
                <p class="text-sm text-center">No hay camiones en la calle en este momento. Todos han finalizado su turno o no se ha despachado ninguno.</p>
            </div>
        @endforelse
    </div>

    @if($showModal)
        @include('livewire.botaderoModal')
    @endif
</div>