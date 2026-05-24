<div class="px-4">
    
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">FLOTA DE VEHÍCULOS</h2>
        <p class="text-sm text-gray-500 mt-1">Administra los camiones recolectores y su estado operativo.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <button wire:click="openModal()" class="w-full md:w-auto bg-orange-500 hover:bg-orange-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-truck-medical"></i> Nuevo Vehículo
        </button>

        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por placa o modelo..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-shadow bg-gray-50 focus:bg-white text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Vehículo (Placa / Modelo)</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider">Capacidad</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider">Tipo / Dimensión</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($camiones as $item)
                        <tr wire:key="camion-{{ $item->id_camion }}" class="hover:bg-orange-50 transition-colors group {{ $item->estado_operativo == 'Fuera de Servicio' ? 'opacity-60 bg-gray-50' : '' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                        <i class="fa-solid fa-truck"></i>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base font-black">{{ $item->placa }}</span>
                                        <span class="text-xs font-normal text-gray-500 mt-0.5">{{ $item->modelo ?: 'Modelo no registrado' }}</span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="font-mono text-sm bg-gray-100 px-3 py-1.5 rounded-lg text-gray-700 border border-gray-200 font-bold">
                                    {{ $item->capacidad_ton }} Ton.
                                </span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center text-gray-600">
                                {{ $item->dimension_tipo }}
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->estado_operativo === 'Operativo')
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                        <i class="fa-solid fa-check-circle mr-1"></i> OPERATIVO
                                    </span>
                                @elseif($item->estado_operativo === 'En Mantenimiento')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold border border-yellow-300">
                                        <i class="fa-solid fa-wrench mr-1"></i> EN TALLER
                                    </span>
                                @else
                                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-bold border border-red-200">
                                        <i class="fa-solid fa-ban mr-1"></i> DE BAJA
                                    </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-1">
                                    
                                    @if($item->estado_operativo === 'Operativo')
                                        <button wire:click.prevent="abrirMantenimiento({{$item->id_camion}})" 
                                            class="text-gray-400 hover:text-yellow-600 transition p-2 rounded-lg hover:bg-yellow-100" title="Enviar a Mantenimiento">
                                            <i class="fa-solid fa-wrench"></i>
                                        </button>
                                    @endif

                                    @if($item->estado_operativo === 'En Mantenimiento' || $item->estado_operativo === 'Fuera de Servicio')
                                        <button wire:click.prevent="marcarOperativo({{$item->id_camion}})" 
                                            wire:confirm="¿Confirmas que este vehículo ya está reparado y operativo?"
                                            class="text-gray-400 hover:text-green-600 transition p-2 rounded-lg hover:bg-green-100" title="Marcar como Operativo">
                                            <i class="fa-solid fa-check-circle"></i>
                                        </button>
                                    @endif

                                    <button wire:click.prevent="editar({{$item->id_camion}})" 
                                        class="text-gray-400 hover:text-orange-600 transition p-2 rounded-lg hover:bg-orange-100" title="Editar Vehículo">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    
                                    @if($item->estado_operativo !== 'Fuera de Servicio')
                                        <button wire:click.prevent="eliminar({{$item->id_camion}})" 
                                            wire:confirm="¿Estás seguro de que deseas dar de baja definitiva a este camión?"
                                            class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-100" title="Dar de Baja">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-300 mb-3"><i class="fa-solid fa-truck-slash text-4xl"></i></div>
                                <p class="text-gray-500">No hay vehículos registrados o no coinciden con la búsqueda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($camiones, 'hasPages') && $camiones->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $camiones->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        @include('livewire.camionModal')
    @endif

    @if($showMantenimientoModal)
        @include('livewire.camionMatenimientoModal')
    @endif
</div>