<div class="px-4 pb-10">
    
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">GESTIÓN DE PERSONAL Y USUARIOS</h2>
        <p class="text-sm text-gray-500 mt-1">Administra los accesos al sistema y al personal operativo de recolección.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        
        <button wire:click="openModal()" class="w-full md:w-auto bg-emap-blue hover:bg-blue-900 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-user-plus"></i> Nuevo Registro
        </button>

        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por nombre o CI..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emap-blue bg-white text-sm shadow-sm">
        </div>
    </div>

    <div class="flex gap-2 border-b border-gray-200 mb-6">
        <button wire:click="setFiltro('sistema')" 
            class="px-5 py-3 text-sm font-bold transition-all border-b-2 {{ $filtro_tipo === 'sistema' ? 'border-emap-blue text-emap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fa-solid fa-laptop-code mr-2"></i> Usuarios del Sistema
        </button>
        <button wire:click="setFiltro('campo')" 
            class="px-5 py-3 text-sm font-bold transition-all border-b-2 {{ $filtro_tipo === 'campo' ? 'border-emap-blue text-emap-blue' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            <i class="fa-solid fa-hard-hat mr-2"></i> Trabajadores de Campo
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden relative">
        <div wire:loading wire:target="setFiltro, search" class="absolute inset-0 bg-white/60 backdrop-blur-sm z-10 flex items-center justify-center">
            <i class="fa-solid fa-spinner fa-spin text-3xl text-emap-blue"></i>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Nombre Completo</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Cargo / Contacto</th>
                        
                        @if($filtro_tipo === 'sistema')
                            <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Credenciales</th>
                        @else
                            <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase">Tarifa por Viaje</th>
                        @endif

                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase">Rol</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($usuarios as $item)
                        <tr wire:key="usuario-{{ $item->id_usuario }}" class="hover:bg-gray-50 transition-colors group {{ $item->estado == 'Inactivo' ? 'opacity-60 bg-gray-50' : '' }}">
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg font-bold
                                        {{ $filtro_tipo === 'sistema' ? 'bg-blue-100 text-emap-blue' : 'bg-green-100 text-emap-green' }}">
                                        {{ substr($item->nombre_completo, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-base">{{ $item->nombre_completo }}</span>
                                        <span class="text-xs font-bold mt-0.5">
                                            @if($item->estado == 'Activo')
                                                <span class="text-green-600"><i class="fa-solid fa-circle text-[8px] mr-1"></i>ACTIVO</span>
                                            @else
                                                <span class="text-red-500"><i class="fa-solid fa-circle text-[8px] mr-1"></i>BAJA</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="font-bold text-gray-700">{{ $item->cargo_base }}</span>
                                    <span class="text-xs text-gray-500 mt-1"><i class="fa-solid fa-phone text-gray-400 mr-1"></i> {{ $item->telefono ?? 'Sin número' }}</span>
                                </div>
                            </td>

                            @if($filtro_tipo === 'sistema')
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->usuario)
                                        <span class="font-mono text-sm font-bold bg-blue-50 text-emap-blue px-2 py-1 rounded border border-blue-100">
                                            <i class="fa-solid fa-at mr-1 opacity-50"></i>{{ $item->usuario }}
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400 italic">Sin acceso</span>
                                    @endif
                                </td>
                            @else
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="bg-green-50 text-emap-green font-bold px-3 py-1.5 rounded-lg border border-green-200">
                                        Bs. {{ number_format($item->tarifa_por_viaje ?? 0, 2) }}
                                    </span>
                                </td>
                            @endif

                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($item->rol === 'Administrador')
                                    <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-purple-200">Admin</span>
                                @elseif($item->rol === 'Supervisor')
                                    <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-blue-200">Supervisor</span>
                                @elseif($item->rol === 'Operario')
                                    <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-yellow-200">Despacho</span>
                                @else
                                    <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-gray-200">Campo</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click.prevent="editar({{$item->id_usuario}})" 
                                        class="text-gray-400 hover:text-emap-blue transition p-2 rounded-lg hover:bg-blue-100" title="Editar">
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                    
                                    @if($item->estado == 'Activo')
                                        <button wire:click.prevent="eliminar({{$item->id_usuario}})" 
                                            wire:confirm="¿Estás seguro de que deseas dar de baja a este personal?"
                                            class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-100" title="Dar de Baja">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    @else
                                        <button wire:click.prevent="restaurar({{$item->id_usuario}})" 
                                            wire:confirm="¿Restaurar a este personal al servicio activo?"
                                            class="text-gray-400 hover:text-green-600 transition p-2 rounded-lg hover:bg-green-100" title="Restaurar">
                                            <i class="fa-solid fa-undo"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="text-gray-200 mb-4"><i class="fa-solid fa-users-slash text-5xl"></i></div>
                                <h3 class="text-lg font-bold text-gray-700 mb-1">Sin resultados</h3>
                                <p class="text-gray-500">No hay {{ $filtro_tipo === 'sistema' ? 'usuarios del sistema' : 'trabajadores de campo' }} registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($usuarios, 'hasPages') && $usuarios->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    @if($showModal)
        @include('livewire.usuarioModal')
    @endif
</div>