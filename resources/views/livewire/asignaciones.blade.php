<div class="px-4 pb-10">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">CONTROL DE DESPACHOS Y ASIGNACIONES</h2>
        <p class="text-sm text-gray-500 mt-1">Gestiona las rutas diarias y toma asistencia del personal.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <button wire:click="openModal()" class="w-full md:w-auto bg-emap-blue hover:bg-blue-900 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-calendar-plus"></i> Nueva Planificación
        </button>

        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por ruta..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emap-blue bg-gray-50 text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Fecha / Turno</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Ruta / Vehículo</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase">Personal</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($asignaciones as $item)
                        <tr wire:key="asignacion-{{ $item->id_asignacion }}" class="hover:bg-blue-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900"><i class="fa-regular fa-calendar text-emap-blue mr-1"></i> {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500 mt-1"><i class="fa-regular fa-clock mr-1"></i> {{ $item->turno }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-800">{{ $item->ruta?->nombre_ruta ?? 'Ruta Eliminada' }}</div>
                                <div class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-truck text-gray-400 mr-1"></i> {{ $item->camion?->placa ?? 'Camión Eliminado' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-100">
                                    <i class="fa-solid fa-users mr-1"></i> {{ $item->detallesCuadrilla->count() }} Asignados
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->estado_operacion == 'Programada')
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-xs font-bold border border-gray-200">PROGRAMADA</span>
                                @elseif($item->estado_operacion == 'En Ruta')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold border border-blue-200"><i class="fa-solid fa-truck-fast mr-1"></i> EN RUTA</span>
                                @elseif($item->estado_operacion == 'Finalizada')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200"><i class="fa-solid fa-check-double mr-1"></i> FINALIZADA</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="abrirAsistencia({{ $item->id_asignacion }})" 
                                        class="text-gray-500 hover:text-emap-blue transition p-2 rounded-lg hover:bg-blue-100" title="Ver Cuadrilla / Tomar Asistencia">
                                        <i class="fa-solid fa-clipboard-user text-lg"></i>
                                    </button>
                                    
                                    @if($item->estado_operacion == 'Programada')
                                        <button wire:click.prevent="eliminar({{$item->id_asignacion}})" 
                                            wire:confirm="¿Seguro que deseas eliminar esta planificación?"
                                            class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-100" title="Eliminar">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button disabled class="text-gray-200 p-2 rounded-lg cursor-not-allowed" title="Registro bloqueado (En Operación o Finalizado)">
                                            <i class="fa-solid fa-lock"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No hay planificaciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($asignaciones, 'hasPages') && $asignaciones->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">{{ $asignaciones->links() }}</div>
        @endif
    </div>

    @if($showModal) @include('livewire.asignacionModal') @endif
    @if($showAsistenciaModal) @include('livewire.asistenciaModal') @endif
</div>