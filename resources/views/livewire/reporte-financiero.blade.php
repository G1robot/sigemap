<div class="px-4 pb-10">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                <i class="fa-solid fa-file-invoice-dollar text-emap-blue mr-2 print:hidden"></i> 
                Reporte Financiero de Planillas
            </h2>
            <p class="text-sm text-gray-500 mt-1 print:hidden">Resumen de pagos liquidados al personal operativo.</p>
            
            <div class="hidden print:block mt-2">
                <p class="text-sm font-bold text-gray-800 uppercase">Estado de Cuenta de Personal</p>
                <p class="text-xs text-gray-600">Periodo liquidado: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="print:hidden bg-emap-blue hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Exportar PDF
            </button>

            <div class="print:hidden bg-white p-3 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center gap-3">
                <div class="flex items-center gap-2 text-emap-blue font-bold px-2">
                    <i class="fa-solid fa-filter"></i>
                    <span class="text-sm uppercase tracking-wider">Filtro:</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-bold">DESDE</span>
                    <input type="date" wire:model.live="fecha_inicio" class="border border-gray-300 rounded-lg p-1.5 text-sm focus:ring-2 focus:ring-emap-gold bg-gray-50 text-gray-700 font-bold">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400 font-bold">HASTA</span>
                    <input type="date" wire:model.live="fecha_fin" class="border border-gray-300 rounded-lg p-1.5 text-sm focus:ring-2 focus:ring-emap-gold bg-gray-50 text-gray-700 font-bold">
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 print:gap-4 print:mb-6">
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 print:border-gray-300 print:shadow-none">
            <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Gasto Total Operativo</p>
            <h3 class="text-3xl font-black text-emap-green mt-1 print:text-black">
                <span class="text-lg">Bs.</span> {{ number_format($kpi_total_pagado, 2) }}
            </h3>
        </div>
        
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 print:border-gray-300 print:shadow-none">
            <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Viajes Remunerados</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1">
                {{ $kpi_viajes_pagados }} <span class="text-lg text-gray-400 font-bold">Rutas</span>
            </h3>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 print:border-gray-300 print:shadow-none">
            <p class="text-xs font-black text-gray-400 uppercase tracking-wider">Personal Beneficiado</p>
            <h3 class="text-3xl font-black text-gray-800 mt-1">
                {{ $kpi_trabajadores }} <span class="text-lg text-gray-400 font-bold">Trabajadores</span>
            </h3>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print:border-none print:shadow-none">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm print:border-collapse print:w-full">
                <thead class="bg-gray-50 print:bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-400 w-1/4">Trabajador</th>
                        <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-400 w-2/4">Desglose de Viajes Liquidados</th>
                        <th class="px-4 py-3 text-right font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-400 w-1/4">Total Pagado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($reporte_detallado as $item)
                        <tr class="hover:bg-gray-50 break-inside-avoid">
                            
                            <td class="px-4 py-4 align-top print:border print:border-gray-300">
                                <div class="font-black text-base text-gray-900 uppercase">{{ $item['usuario']->nombre_completo }}</div>
                                <div class="text-xs text-gray-500 font-bold mt-0.5">CI: {{ $item['usuario']->ci }}</div>
                                <div class="mt-2 inline-block bg-gray-100 px-2 py-1 rounded text-xs font-bold text-gray-600 border border-gray-200">
                                    {{ $item['usuario']->cargo_base }}
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1">Tarifa base: Bs. {{ number_format($item['usuario']->tarifa_por_viaje, 2) }}</p>
                            </td>

                            <td class="px-4 py-4 align-top print:border print:border-gray-300 print:p-2">
                                <div class="text-xs text-gray-500 font-bold mb-2 uppercase border-b pb-1 print:border-gray-300">
                                    Detalle de {{ $item['cantidad_rutas'] }} operaciones remuneradas:
                                </div>
                                <ul class="space-y-1">
                                    @foreach($item['desglose'] as $pago)
                                        <li class="flex justify-between items-center text-xs py-1 px-2 hover:bg-gray-100 rounded">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-regular fa-calendar-check text-emap-green print:text-black w-3"></i>
                                                <span class="font-bold text-gray-700">
                                                    {{ \Carbon\Carbon::parse($pago->asignacion->fecha)->format('d/m/Y') }}
                                                </span>
                                                <span class="text-gray-500 hidden sm:inline">- {{ $pago->asignacion->ruta->nombre_ruta ?? 'Ruta N/A' }}</span>
                                            </div>
                                            <span class="font-black text-gray-600">Bs. {{ number_format($pago->monto_pago, 2) }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>

                            <td class="px-4 py-4 align-top text-right print:border print:border-gray-300">
                                <div class="bg-green-50 text-green-800 font-black text-lg p-3 rounded-lg border border-green-200 inline-block print:border-none print:bg-transparent print:text-black">
                                    <span class="text-sm font-bold">Bs.</span> {{ number_format($item['total_ganado'], 2) }}
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                No se encontraron registros de pagos liquidados en este periodo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if(count($reporte_detallado) > 0)
                <tfoot class="bg-gray-50 print:bg-gray-100">
                    <tr>
                        <td colspan="2" class="px-4 py-4 text-right font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-400">
                            GRAN TOTAL LIQUIDADO EN EL PERIODO:
                        </td>
                        <td class="px-4 py-4 text-right font-black text-xl text-emap-blue print:border print:border-gray-400 print:text-black">
                            Bs. {{ number_format($kpi_total_pagado, 2) }}
                        </td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
    
    <div class="hidden print:flex justify-between mt-24 px-12">
        <div class="text-center border-t border-gray-400 w-48 pt-2">
            <p class="font-bold text-xs text-gray-800">Elaborado por:</p>
            <p class="text-xs text-gray-600">{{ Auth::user()->nombre_completo ?? 'Sistema SIG-EMAP' }}</p>
        </div>
        <div class="text-center border-t border-gray-400 w-48 pt-2">
            <p class="font-bold text-xs text-gray-800">Visto Bueno:</p>
            <p class="text-xs text-gray-600">Gerencia de Operaciones</p>
        </div>
    </div>
</div>