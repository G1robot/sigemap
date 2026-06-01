<div class="px-4 pb-10">
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">CONTROL DE CONTINGENCIAS Y PAROS</h2>
        <p class="text-sm text-gray-500 mt-1">Registra días con bloqueos, feriados o problemas sociales que afecten las rutas.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mb-6">
        <button wire:click="openModal" class="w-full md:w-auto bg-red-500 hover:bg-red-600 text-white font-bold py-2.5 px-6 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Registrar Alerta
        </button>

        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar por motivo..." class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-red-500 bg-white text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Fecha Afectada</th>
                    <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase">Motivo / Descripción</th>
                    <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase">Nivel de Bloqueo</th>
                    <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white">
                @forelse($contingencias as $item)
                    <tr wire:key="contingencia-{{ $item->id_contingencia }}" class="hover:bg-red-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-800">
                            <i class="fa-regular fa-calendar-xmark text-red-400 mr-2"></i> {{ \Carbon\Carbon::parse($item->fecha)->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-gray-600">
                            {{ $item->descripcion }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if($item->bloqueo_total)
                                <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-black uppercase border border-red-200">
                                    <i class="fa-solid fa-ban mr-1"></i> Bloqueo Total
                                </span>
                            @else
                                <span class="bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-xs font-bold uppercase border border-yellow-200">
                                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Parcial / Feriado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button wire:click="editar({{ $item->id_contingencia }})" class="text-gray-400 hover:text-blue-600 p-2"><i class="fa-solid fa-pen"></i></button>
                            <button wire:click="eliminar({{ $item->id_contingencia }})" wire:confirm="¿Borrar este registro?" class="text-gray-400 hover:text-red-600 p-2"><i class="fa-solid fa-trash-can"></i></button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-6 py-12 text-center text-gray-500">No hay contingencias registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($contingencias->hasPages()) <div class="px-6 py-4 border-t border-gray-100">{{ $contingencias->links() }}</div> @endif
    </div>

    @if($showModal) @include('livewire.contingenciaModal') @endif
</div>