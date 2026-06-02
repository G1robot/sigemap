<div>
    <div class="px-4 pb-10 print:hidden">
        <div class="mb-8 flex justify-between items-end">
            <div>
                <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                    <i class="fa-solid fa-file-invoice-dollar text-emap-blue mr-2"></i> Liquidación de Planillas
                </h2>
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
                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emap-blue bg-white text-sm shadow-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse($liquidaciones as $item)
                @php 
                    $tieneDeuda = $item['cantidad_viajes'] > 0; 
                @endphp
                
                <div wire:key="liq-{{ $item['usuario']->id_usuario }}" class="bg-white rounded-2xl shadow-sm border {{ $tieneDeuda ? 'border-blue-200 shadow-blue-100' : 'border-gray-100 opacity-75' }} overflow-hidden transition-all relative">
                    
                    @if(!$tieneDeuda)
                        <div class="absolute top-4 right-4 bg-gray-100 text-gray-500 text-xs font-bold px-2 py-1 rounded">
                            <i class="fa-solid fa-check-circle text-green-500"></i> AL DÍA
                        </div>
                    @endif

                    <div class="p-6">
                        <div class="flex items-center gap-4 mb-5 pb-4 border-b border-gray-100">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-xl font-bold
                                {{ $item['usuario']->cargo_base === 'Chofer' ? 'bg-emap-blue text-white' : 'bg-blue-100 text-emap-blue' }}">
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
                                            class="text-emap-blue hover:text-blue-800 bg-blue-50 hover:bg-blue-100 p-1.5 rounded transition-colors" title="Ver desglose de rutas">
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
                                <span class="text-2xl font-black {{ $tieneDeuda ? 'text-emap-green' : 'text-gray-400' }}">
                                    Bs. {{ number_format($item['total_a_pagar'], 2) }}
                                </span>
                            </div>
                        </div>

                        @if($tieneDeuda)
                            <button wire:click="liquidarPago({{ $item['usuario']->id_usuario }})" 
                                wire:confirm="Se registrará un pago de Bs. {{ number_format($item['total_a_pagar'], 2) }}. ¿Deseas procesar y generar la boleta?"
                                wire:loading.attr="disabled" 
                                wire:target="liquidarPago({{ $item['usuario']->id_usuario }})"
                                class="w-full bg-emap-green hover:bg-green-700 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-all flex items-center justify-center gap-2">
                                
                                <span wire:loading.remove wire:target="liquidarPago({{ $item['usuario']->id_usuario }})">
                                    <i class="fa-solid fa-print"></i> Pagar e Imprimir
                                </span>
                                <span wire:loading wire:target="liquidarPago({{ $item['usuario']->id_usuario }})">
                                    <i class="fa-solid fa-spinner fa-spin"></i> Imprimiendo...
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

    @if($boleta_impresion)
    <div class="hidden print:block w-full bg-white text-black p-8 max-w-4xl mx-auto border-2 border-black" style="font-family: Arial, sans-serif;">
        
        <div class="flex justify-between items-center border-b-2 border-black pb-6 mb-6">
            <img src="{{ asset('img/logo_emap.png') }}" alt="EMAP Logo" class="h-20 grayscale" style="filter: grayscale(100%);">
            <div class="text-right">
                <h1 class="text-2xl font-black uppercase tracking-wider">Boleta de Liquidación</h1>
                <p class="text-sm font-bold text-gray-600 mt-1">Empresa Municipal de Aseo Potosí (EMAP)</p>
                <p class="text-sm font-mono mt-1">RECIBO N°: <strong>{{ $boleta_impresion['recibo_nro'] }}</strong></p>
                <p class="text-sm mt-1">Fecha Emisión: {{ $boleta_impresion['fecha_emision'] }}</p>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-sm font-bold uppercase border-b border-gray-300 pb-1 mb-3">Datos del Beneficiario</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><span class="font-bold">Nombre Completo:</span> <span class="uppercase">{{ $boleta_impresion['nombre'] }}</span></div>
                <div><span class="font-bold">Cédula de Identidad:</span> {{ $boleta_impresion['ci'] }}</div>
                <div><span class="font-bold">Cargo Operativo:</span> <span class="uppercase">{{ $boleta_impresion['cargo'] }}</span></div>
            </div>
        </div>

        <div class="mb-12">
            <h3 class="text-sm font-bold uppercase border-b border-gray-300 pb-1 mb-3">Detalle de Liquidación</h3>
            <table class="w-full text-left text-sm border-collapse border border-black mb-4">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-black p-2">Concepto</th>
                        <th class="border border-black p-2 text-center">Cantidad</th>
                        <th class="border border-black p-2 text-right">Tarifa Unitaria (Bs.)</th>
                        <th class="border border-black p-2 text-right">Subtotal (Bs.)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-black p-2 uppercase">Servicio de Recolección (Rutas Completadas)</td>
                        <td class="border border-black p-2 text-center font-bold">{{ $boleta_impresion['cantidad_viajes'] }}</td>
                        <td class="border border-black p-2 text-right">{{ number_format($boleta_impresion['tarifa'], 2) }}</td>
                        <td class="border border-black p-2 text-right">{{ number_format($boleta_impresion['total_pagado'], 2) }}</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="border border-black p-3 text-right font-black uppercase text-base">TOTAL LIQUIDO PAGABLE:</td>
                        <td class="border border-black p-3 text-right font-black text-lg">Bs. {{ number_format($boleta_impresion['total_pagado'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
            <p class="text-xs italic mt-2">Los fondos fueron entregados en efectivo conforme a los registros del sistema SIG-EMAP.</p>
        </div>

        <div class="flex justify-between mt-24 px-10">
            <div class="text-center w-64 border-t border-black pt-2">
                <p class="font-bold text-sm uppercase">Recibí Conforme</p>
                <p class="text-xs mt-1">{{ $boleta_impresion['nombre'] }}</p>
                <p class="text-xs">C.I. {{ $boleta_impresion['ci'] }}</p>
            </div>
            <div class="text-center w-64 border-t border-black pt-2">
                <p class="font-bold text-sm uppercase">Entregué Conforme</p>
                <p class="text-xs mt-1">Firma / Sello de Caja</p>
                <p class="text-xs">EMAP Potosí</p>
            </div>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            // El backend nos avisa cuando la boleta está lista para imprimir
            Livewire.on('imprimir-boleta', () => {
                // Le damos 300 milisegundos al HTML para que dibuje el recibo antes de lanzar la ventana de impresión
                setTimeout(() => {
                    window.print();
                }, 300);
            });
        });
    </script>
</div>