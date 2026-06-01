<div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-60 backdrop-blur-sm z-50 animate-fade-in-down p-4">
    <div class="max-w-2xl w-full bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-blue-500">
        
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-gray-800"><i class="fa-solid fa-file-invoice mr-2 text-blue-500"></i> Desglose de Liquidación</h2>
                <p class="text-xs text-gray-500 mt-1">Rutas completadas pendientes de pago</p>
            </div>
            <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
        </div>

        <div class="p-6">
            <div class="flex items-center gap-3 mb-6 bg-blue-50 p-3 rounded-lg border border-blue-100">
                <div class="w-10 h-10 rounded-full bg-blue-200 text-blue-700 flex items-center justify-center font-bold text-lg">
                    {{ substr($usuario_detalle->nombre_completo, 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-black text-blue-900">{{ $usuario_detalle->nombre_completo }}</p>
                    <p class="text-xs text-blue-700">Cargo: {{ $usuario_detalle->cargo_base }} | Tarifa: Bs. {{ number_format($usuario_detalle->tarifa_por_viaje, 2) }}</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left font-bold text-gray-600 uppercase text-xs">Fecha de Operación</th>
                            <th class="px-4 py-2 text-left font-bold text-gray-600 uppercase text-xs">Ruta Asignada</th>
                            <th class="px-4 py-2 text-center font-bold text-gray-600 uppercase text-xs">Rol en Viaje</th>
                            <th class="px-4 py-2 text-right font-bold text-gray-600 uppercase text-xs">Monto a Sumar</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($viajes_detalle_modal as $viaje)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-800 font-medium">
                                    <i class="fa-regular fa-calendar-check text-green-500 mr-1"></i> 
                                    {{ \Carbon\Carbon::parse($viaje->asignacion->fecha)->format('d/m/Y') }}
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    {{ $viaje->asignacion->ruta->nombre_ruta ?? 'Ruta Eliminada' }}
                                    <span class="block text-[10px] text-gray-400">{{ $viaje->asignacion->turno }}</span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600">
                                    {{ $viaje->rol_en_viaje }}
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-600">
                                    Bs. {{ number_format($usuario_detalle->tarifa_por_viaje, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="3" class="px-4 py-3 text-right font-black text-gray-700 uppercase text-xs">
                                Total a Liquidar ({{ count($viajes_detalle_modal) }} viajes)
                            </td>
                            <td class="px-4 py-3 text-right font-black text-gray-900 text-base">
                                Bs. {{ number_format(count($viajes_detalle_modal) * $usuario_detalle->tarifa_por_viaje, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
            <button wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-100 rounded-lg shadow-sm">
                Cerrar Detalle
            </button>
            <button wire:click="liquidarPago({{ $usuario_detalle->id_usuario }})" 
                wire:loading.attr="disabled"
                class="px-5 py-2.5 text-sm font-bold text-white bg-green-500 hover:bg-green-600 rounded-lg shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-check"></i> Liquidar Ahora
            </button>
        </div>
    </div>
</div>