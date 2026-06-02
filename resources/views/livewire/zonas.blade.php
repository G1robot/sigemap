<div class="px-4 pb-10">
    
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">GESTIÓN DE ZONAS</h2>
        <p class="text-sm text-gray-500 mt-1">Administra los distritos o sectores de recolección de la ciudad.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <button wire:click="openModal()" class="w-full md:w-auto bg-emap-blue hover:bg-blue-900 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-map-location-dot"></i> Nueva Zona
        </button>

        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por nombre de zona..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emap-blue transition-shadow bg-gray-50 focus:bg-white text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider w-1/3">Nombre de la Zona</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Descripción o Detalles</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase tracking-wider w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($zonas as $item)
                        <tr wire:key="zona-{{ $item->id_zona }}" class="hover:bg-blue-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-emap-blue border border-blue-200 shadow-inner">
                                        <i class="fa-solid fa-map"></i>
                                    </div>
                                    <span class="text-base font-black">{{ $item->nombre_zona }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 font-medium">
                                {{ $item->descripcion ?: 'Sin descripción adicional' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click.prevent="editar({{$item->id_zona}})" 
                                        class="text-gray-400 hover:text-emap-blue transition p-2 rounded-lg hover:bg-blue-100" title="Editar Zona">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    
                                    <button wire:click.prevent="eliminar({{$item->id_zona}})" 
                                        wire:confirm="¿Estás seguro de eliminar esta zona?"
                                        class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-100" title="Eliminar Zona">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-16 text-center">
                                <div class="text-gray-200 mb-4"><i class="fa-solid fa-map-location-dot text-5xl"></i></div>
                                <h3 class="text-lg font-bold text-gray-700 mb-1">Sin resultados</h3>
                                <p class="text-gray-500">No hay zonas territoriales registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($zonas, 'hasPages') && $zonas->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $zonas->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        @include('livewire.zonaModal')
    @endif
</div>