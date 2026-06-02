<div class="px-4 pb-10">
    
    <div class="mb-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight uppercase">
                <i class="fa-solid fa-truck-moving text-emap-blue mr-2 print:hidden"></i> 
                Reporte de Rendimiento de Flota
            </h2>
            <p class="text-sm text-gray-500 mt-1 print:hidden">Análisis de operatividad y mantenimiento vehicular.</p>
            
            <p class="hidden print:block text-sm font-bold text-gray-600 mt-1">
                Periodo evaluado: {{ \Carbon\Carbon::parse($fecha_inicio)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($fecha_fin)->format('d/m/Y') }}
            </p>
        </div>

        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="print:hidden bg-emap-blue hover:bg-blue-800 text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-all flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Exportar PDF
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

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden print:border-none print:shadow-none">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm print:border-collapse print:w-full">
                <thead class="bg-gray-50 print:bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-300">Vehículo / Placa</th>
                        <th class="px-4 py-3 text-center font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-300">Rendimiento Operativo</th>
                        <th class="px-4 py-3 text-left font-black text-gray-600 uppercase tracking-wider print:border print:border-gray-300">Historial de Mantenimientos (En el periodo)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($camiones as $camion)
                        
                        @php 
                            $tieneMantenimientos = $camion->historialMantenimiento->count() > 0;
                            // Si hizo 0 viajes y tuvo mantenimientos, es una señal crítica
                            $alertaCritica = ($camion->viajes_completados == 0 && $tieneMantenimientos);
                        @endphp

                        <tr class="hover:bg-gray-50 {{ $alertaCritica ? 'bg-red-50/50 print:bg-red-50' : '' }}">
                            
                            <td class="px-4 py-4 align-top print:border print:border-gray-300">
                                <div class="font-black text-lg text-gray-900">{{ $camion->placa }}</div>
                                <div class="text-xs text-gray-500 font-bold mt-1 uppercase">{{ $camion->modelo ?: 'Sin modelo' }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-200 font-bold">Capacidad: {{ $camion->capacidad_ton }}T</span>
                                </div>
                                <div class="mt-2 print:hidden">
                                    @if($camion->estado_operativo === 'Operativo')
                                        <span class="text-[10px] font-black text-green-600 bg-green-100 px-2 py-1 rounded-full uppercase tracking-wider"><i class="fa-solid fa-check mr-1"></i> Operativo Actual</span>
                                    @else
                                        <span class="text-[10px] font-black text-red-600 bg-red-100 px-2 py-1 rounded-full uppercase tracking-wider"><i class="fa-solid fa-wrench mr-1"></i> En Taller Actual</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top text-center print:border print:border-gray-300">
                                <div class="flex flex-col gap-3">
                                    <div class="bg-blue-50 p-2 rounded-lg border border-blue-100 print:border-none print:bg-transparent">
                                        <p class="text-[10px] text-blue-600 font-black uppercase tracking-wider">Viajes Completados</p>
                                        <p class="text-2xl font-black text-gray-800">{{ $camion->viajes_completados }}</p>
                                    </div>
                                    <div class="bg-orange-50 p-2 rounded-lg border border-orange-100 print:border-none print:bg-transparent">
                                        <p class="text-[10px] text-orange-600 font-black uppercase tracking-wider">Basura Recolectada</p>
                                        <p class="text-xl font-black text-gray-800">{{ number_format($camion->toneladas_recolectadas, 2) }} <span class="text-sm text-gray-500">Ton</span></p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-4 align-top print:border print:border-gray-300">
                                @if($tieneMantenimientos)
                                    <div class="mb-2">
                                        <span class="bg-red-100 text-red-700 text-xs font-black px-2 py-1 rounded border border-red-200">
                                            Ingresó {{ $camion->historialMantenimiento->count() }} veces al taller
                                        </span>
                                    </div>
                                    <ul class="space-y-2 mt-3">
                                        @foreach($camion->historialMantenimiento as $mant)
                                            <li class="text-xs bg-gray-50 p-2 rounded border border-gray-200 print:border-none print:bg-transparent print:p-0 print:mb-2">
                                                <div class="font-bold text-gray-700 mb-0.5">
                                                    <i class="fa-regular fa-calendar text-red-500 mr-1 print:text-black"></i> 
                                                    {{ \Carbon\Carbon::parse($mant->fecha_ingreso)->format('d/m/Y') }}
                                                </div>
                                                <div class="text-gray-600 pl-4 border-l-2 border-red-200 print:border-gray-300 ml-1.5 italic">
                                                    "{{ $mant->descripcion ?: 'Ingreso a revisión sin descripción detallada.' }}"
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="h-full flex items-center justify-center text-gray-400 text-xs italic bg-gray-50 rounded-lg border border-gray-100 p-4 print:bg-transparent print:border-none">
                                        <div class="text-center">
                                            <i class="fa-solid fa-shield-check text-green-500 text-lg mb-1 print:text-black"></i>
                                            <p>Sin fallas ni mantenimientos en este periodo.</p>
                                        </div>
                                    </div>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">No hay camiones registrados en la flota.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>