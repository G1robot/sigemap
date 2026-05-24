<div class="px-4">
    
    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">CATÁLOGO DE RUTAS</h2>
        <p class="text-sm text-gray-500 mt-1">Visualiza y gestiona los recorridos espaciales registrados en la ciudad.</p>
    </div>

    <div class="flex flex-col md:flex-row justify-end items-center gap-4 mb-6">
        <div class="relative w-full md:w-80">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </div>
            <input type="text" wire:model.live.debounce.300ms="search" 
                placeholder="Buscar por nombre de ruta..." 
                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-orange-500 bg-gray-50 text-sm">
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Identificador / Nombre</th>
                        <th class="px-6 py-3 text-left font-bold text-gray-500 uppercase tracking-wider">Zona Política</th>
                        <th class="px-6 py-3 text-center font-bold text-gray-500 uppercase tracking-wider">Horario de Recojo</th>
                        <th class="px-6 py-3 text-right font-bold text-gray-500 uppercase tracking-wider w-24">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($rutas as $item)
                        <tr wire:key="ruta-{{ $item->id_ruta }}" class="hover:bg-orange-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-bold text-gray-900 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600">
                                        <i class="fa-solid fa-route"></i>
                                    </div>
                                    <span class="text-base font-black">{{ $item->nombre_ruta }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 whitespace-nowrap font-medium">
                                <i class="fa-solid fa-location-crosshairs text-gray-400 mr-1"></i>
                                {{ $item->zona?->nombre_zona ?: 'Sin zona asignada' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold border border-gray-200">
                                    <i class="fa-regular fa-clock mr-1 text-orange-500"></i> {{ $item->horario_permitido }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end gap-1">
                                    <button wire:click="verMapa({{ $item->id_ruta }})" 
                                        class="text-gray-400 hover:text-blue-600 transition p-2 rounded-lg hover:bg-blue-100" title="Ver trazado geográfico">
                                        <i class="fa-solid fa-eye text-base"></i>
                                    </button>
                                    
                                    <button wire:click.prevent="eliminar({{$item->id_ruta}})" 
                                        wire:confirm="¿Estás seguro de eliminar permanentemente esta ruta y sus datos geográficos de PostGIS?"
                                        class="text-gray-400 hover:text-red-600 transition p-2 rounded-lg hover:bg-red-100" title="Eliminar Recorrido">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="text-gray-300 mb-3"><i class="fa-solid fa-map-marked-alt text-4xl"></i></div>
                                <p class="text-gray-500">No hay rutas espaciales trazadas aún.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if(method_exists($rutas, 'hasPages') && $rutas->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $rutas->links() }}
            </div>
        @endif
    </div>

    @if($showMapModal)
        <div class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 backdrop-blur-sm z-50 animate-fade-in-down">
            <div class="max-w-4xl w-full mx-4 bg-white rounded-2xl shadow-2xl overflow-hidden border-t-4 border-blue-500">
                
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                    <div>
                        <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <i class="fa-solid fa-map-location-dot text-blue-500"></i>
                            Visor Geográfico: {{ $nombre_ruta_ver }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-0.5">Pertenece a: {{ $zona_ruta_ver }}</p>
                    </div>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-red-500 transition text-xl">&times;</button>
                </div>

                <div class="p-4 bg-white">
                    <div wire:ignore class="h-[450px] w-full rounded-xl border border-gray-200 overflow-hidden relative z-0">
                        <div id="mapa_visor" class="h-full w-full"></div>
                    </div>
                </div>

                <div class="bg-gray-50 px-6 py-3 flex justify-end border-t border-gray-100">
                    <button type="button" wire:click="closeModal" class="px-5 py-2.5 text-sm font-bold text-white bg-gray-700 hover:bg-gray-800 rounded-lg transition-colors shadow-sm">
                        Cerrar Visor
                    </button>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:initialized', () => {
            var mapVisor = null;
            var capaRuta = null;

            Livewire.on('dibujar-ruta-guardada', (event) => {
                setTimeout(() => {
                    
                    if (mapVisor !== null) {
                        mapVisor.remove();
                    }

                    mapVisor = L.map('mapa_visor').setView([-19.5836, -65.7531], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '© SIG-EMAP'
                    }).addTo(mapVisor);

                    var geojsonData = JSON.parse(event.geojson);

                    capaRuta = L.geoJSON(geojsonData, {
                        style: {
                            color: '#0284c7',
                            weight: 6,
                            opacity: 0.85
                        }
                    }).addTo(mapVisor);

                    if (capaRuta) {
                        mapVisor.fitBounds(capaRuta.getBounds(), { padding: [30, 30] });
                    }

                }, 200);
            });
        });
    </script>
</div>