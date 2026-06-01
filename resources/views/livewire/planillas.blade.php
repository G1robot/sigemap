<div class="px-4 pb-10">
    <div class="mb-8 flex justify-between items-end">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">LIQUIDACIÓN DE PLANILLAS</h2>
            <p class="text-sm text-gray-500 mt-1">Control de pagos al personal operativo por rutas completadas.</p>
        </div>
    </div>

    <div class="flex justify-end mb-6">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar trabajador..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-green-500 bg-white text-sm shadow-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($liquidaciones as $item)
            @php 
                $tieneDeuda = $item['cantidad_viajes'] > 0; 
            @endphp
            
            <!-- EL WIRE:KEY ES VITAL AQUÍ -->
            <div wire:key="liq-{{ $item['usuario']->id_usuario }}" class="bg-white rounded-2xl shadow-sm border {{ $tieneDeuda ? 'border-green-200 shadow-green-100' : 'border-gray-100 opacity-75' }} overflow-hidden transition-all relative">
                
                @if(!$tieneDeuda)
                    <div class="absolute top-4 right-4 bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded">
                        <i class="fa-solid fa-check-circle"></i> AL DÍA
                    </div>
                @endif

                <div class="p-6">
                    <div class="flex items-center gap-4 mb-5 pb-4 border-b border-gray-100">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                            {{ $item['usuario']->cargo_base === 'Chofer' ? 'bg-orange-100 text-orange-600' : 'bg-blue-100 text-blue-600' }}">
                            {{ substr($item['usuario']->nombre_completo, 0, 1) }}
                        </div>
                        <div>
                            <h3 class="font-black text-gray-800 text-lg leading-tight">{{ $item['usuario']->nombre_completo }}</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ $item['usuario']->cargo_base }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500"><i class="fa-solid fa-route w-4"></i> Viajes a Liquidar:</span>
                            
                            <div class="flex items-center gap-2">
                                <span class="font-black {{ $tieneDeuda ? 'text-gray-800' : 'text-gray-400' }} text-lg">{{ $item['cantidad_viajes'] }}</span>
                                
                                @if($tieneDeuda)
                                    <button wire:click="verDetalles({{ $item['usuario']->id_usuario }})" 
                                        class="text-blue-500 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Ver desglose de rutas">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-sm">
                            <span class="text-gray-500"><i class="fa-solid fa-money-bill-wave w-4"></i> Tarifa por Ruta:</span>
                            <span class="font-bold text-gray-600">Bs. {{ number_format($item['tarifa_aplicada'], 2) }}</span>
                        </div>
                        
                        <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 flex justify-between items-center mt-2">
                            <span class="text-xs font-black text-gray-400 uppercase tracking-wider">Total a Pagar</span>
                            <span class="text-2xl font-black {{ $tieneDeuda ? 'text-green-600' : 'text-gray-400' }}">
                                Bs. {{ number_format($item['total_a_pagar'], 2) }}
                            </span>
                        </div>
                    </div>

                    @if($tieneDeuda)
                        <button wire:click="liquidarPago({{ $item['usuario']->id_usuario }})" 
                            wire:confirm="¿Confirmas el pago de Bs. {{ number_format($item['total_a_pagar'], 2) }} a este trabajador por {{ $item['cantidad_viajes'] }} viajes realizados?"
                            wire:loading.attr="disabled" 
                            wire:target="liquidarPago({{ $item['usuario']->id_usuario }})"
                            class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                            
                            <span wire:loading.remove wire:target="liquidarPago({{ $item['usuario']->id_usuario }})">
                                <i class="fa-solid fa-hand-holding-dollar text-lg"></i> Procesar Pago
                            </span>
                            <span wire:loading wire:target="liquidarPago({{ $item['usuario']->id_usuario }})">
                                <i class="fa-solid fa-spinner fa-spin"></i> Liquidando...
                            </span>
                        </button>
                    @else
                        <button disabled class="w-full bg-gray-100 text-gray-400 font-bold py-3 px-4 rounded-xl flex items-center justify-center gap-2 cursor-not-allowed">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Sin deuda pendiente
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12 flex flex-col items-center justify-center text-gray-400">
                <i class="fa-solid fa-users-slash text-5xl mb-4 text-gray-200"></i>
                <h3 class="text-lg font-bold text-gray-600 mb-1">Personal no encontrado</h3>
                <p class="text-sm">No se encontró personal operativo en el sistema.</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($personal, 'hasPages') && $personal->hasPages())
        <div class="mt-8 border-t border-gray-100 pt-6">
            {{ $personal->links() }}
        </div>
    @endif

    @if($showModal)
        @include('livewire.planillasModal')
    @endif
</div>