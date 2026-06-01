<div class="px-4 pb-8 h-[calc(100vh-120px)] flex flex-col">
    
    <div class="mb-4">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">CENTRO DE CONTROL ESPACIAL</h2>
        <p class="text-sm text-gray-500 mt-1">Gestión topológica y trazado de rutas de recolección.</p>
    </div>

    <div class="flex-1 grid grid-cols-1 lg:grid-cols-3 gap-6 min-h-[600px]">
        
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-full">
            
            @if($modo === 'lista')
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Lista de Rutas</h3>
                    <button wire:click="cambiarModo('crear')" class="bg-orange-500 hover:bg-orange-600 text-white text-xs font-bold py-2 px-3 rounded-lg transition-colors shadow-sm">
                        <i class="fa-solid fa-plus mr-1"></i> Nuevo Trazado
                    </button>
                </div>

                <div class="p-4 border-b border-gray-100">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Buscar ruta..." class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-orange-500 bg-gray-50 text-sm">
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-2">
                    @forelse($rutas as $item)
                        <div wire:key="ruta-{{ $item->id_ruta }}" 
                             wire:click="verMapa({{ $item->id_ruta }})"
                             class="p-4 mb-2 rounded-xl border border-gray-100 hover:border-orange-300 hover:bg-orange-50 cursor-pointer transition-all group">
                            
                            <div class="flex justify-between items-start mb-2">
                                <div class="font-black text-gray-800 text-base leading-tight group-hover:text-orange-700 transition-colors">
                                    {{ $item->nombre_ruta }}
                                </div>
                                <button wire:click.stop="eliminar({{ $item->id_ruta }})" wire:confirm="¿Eliminar definitivamente esta ruta?" class="text-gray-400 hover:text-red-500 transition-colors p-1">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                            
                            <div class="flex flex-col gap-1 text-xs text-gray-500 font-medium mt-2">
                                <span><i class="fa-solid fa-location-crosshairs w-4 text-gray-400"></i> {{ $item->zona?->nombre_zona ?: 'Sin zona' }}</span>
                                <span><i class="fa-regular fa-clock w-4 text-gray-400"></i> {{ $item->horario_permitido }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center p-8 text-gray-400">
                            <i class="fa-solid fa-route text-3xl mb-2"></i>
                            <p class="text-sm">No hay rutas registradas.</p>
                        </div>
                    @endforelse
                </div>

                @if(method_exists($rutas, 'hasPages') && $rutas->hasPages())
                    <div class="p-3 border-t border-gray-100 bg-gray-50">
                        {{ $rutas->links(data: ['scrollTo' => false]) }}
                    </div>
                @endif
            @endif

            @if($modo === 'crear')
                <div class="p-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700"><i class="fa-solid fa-pen-ruler mr-2 text-orange-500"></i> Diseñar Ruta</h3>
                    <button wire:click="cambiarModo('lista')" class="text-gray-500 hover:text-gray-800 text-sm font-bold transition-colors">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Volver
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-6">
                    <form wire:submit.prevent="guardarRuta" class="space-y-5">
                        
                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Zona Asignada *</label>
                            <select wire:model="id_zona" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 text-sm">
                                <option value="">Seleccione zona...</option>
                                @foreach($zonas as $zona)
                                    <option value="{{ $zona->id_zona }}">{{ $zona->nombre_zona }}</option>
                                @endforeach
                            </select>
                            @error('id_zona') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre de la Ruta *</label>
                            <input type="text" wire:model="nombre_ruta" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 text-sm">
                            @error('nombre_ruta') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Turno *</label>
                            <select wire:model="horario_permitido" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-orange-500 text-sm">
                                <option value="">Seleccione turno...</option>
                                <option value="Mañana (07:00 - 13:00)">Mañana (07:00 - 13:00)</option>
                                <option value="Tarde (13:00 - 19:00)">Tarde (13:00 - 19:00)</option>
                                <option value="Noche (19:00 - 01:00)">Noche (19:00 - 01:00)</option>
                            </select>
                            @error('horario_permitido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <p class="text-xs text-gray-600 mb-3 bg-orange-50 p-3 rounded-lg border border-orange-100 leading-relaxed">
                                <i class="fa-solid fa-mouse-pointer text-orange-500 mr-1"></i>
                                Dibuja la ruta en el mapa de la derecha haciendo clic en las calles.
                            </p>
                            @error('coordenadas_geojson') <span class="text-red-500 text-xs font-bold mb-3 block text-center">{{ $message }}</span> @enderror
                            
                            <button type="submit" wire:loading.attr="disabled" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                                <span wire:loading.remove><i class="fa-solid fa-save"></i> Guardar Ruta</span>
                                <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Guardando...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <div class="lg:col-span-2 relative bg-gray-100 rounded-xl border border-gray-200 overflow-hidden shadow-inner h-full z-0">
            
            <div wire:ignore class="h-full w-full relative">
                
                <div id="controles-mapa-crear" class="absolute top-4 right-4 z-[1000] flex gap-2 transition-opacity duration-300" style="display: none;">
                    <button type="button" id="btn-deshacer" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-2 px-4 rounded-lg shadow-lg text-sm transition-all">
                        <i class="fa-solid fa-rotate-left"></i> Deshacer
                    </button>
                    <button type="button" id="btn-limpiar" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-2 px-4 rounded-lg shadow-lg text-sm transition-all">
                        <i class="fa-solid fa-trash-can"></i> Limpiar
                    </button>
                </div>

                <div id="mapa_principal" class="h-full w-full z-10"></div>
                
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // 1. Inicialización base del mapa
            var map = L.map('mapa_principal').setView([-19.5836, -65.7531], 14);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© SIG-EMAP'
            }).addTo(map);

            // 2. Control de Trazado (Leaflet Routing Machine) - Para Crear
            var routingControl = L.Routing.control({
                waypoints: [],
                routeWhileDragging: true,
                language: 'es',
                show: false, 
                addWaypoints: true,
                fitSelectedRoutes: true,
                lineOptions: { styles: [{color: '#f97316', opacity: 0.8, weight: 6}] },
                createMarker: function(i, wp, nWps) {
                    var color = (i === 0) ? 'green' : (i === nWps - 1) ? 'red' : 'blue';
                    return L.marker(wp.latLng, {draggable: true}).bindPopup('Punto ' + (i + 1));
                }
            });

            // 3. Capa GeoJSON - Para Visualizar
            var geojsonLayer = L.geoJSON(null, {
                style: { color: '#0284c7', weight: 6, opacity: 0.85 }
            }).addTo(map);

            // 4. Lógica de captura de puntos (Solo funciona si routingControl está añadido)
            map.on('click', function(e) {
                // Solo agregar puntos si estamos en modo creación (el control está en el mapa)
                if (map.hasLayer(routingControl) || document.getElementById('controles-mapa-crear').style.display === 'flex') {
                    var waypoints = routingControl.getWaypoints().filter(wp => wp.latLng !== null);
                    waypoints.push(L.Routing.waypoint(e.latlng));
                    routingControl.setWaypoints(waypoints);
                }
            });

            // Capturar el GeoJSON generado
            routingControl.on('routesfound', function(e) {
                var coordinates = e.routes[0].coordinates; 
                var geojson = {
                    "type": "LineString",
                    "coordinates": coordinates.map(c => [c.lng, c.lat])
                };
                @this.set('coordenadas_geojson', JSON.stringify(geojson));
            });

            // 5. Botones del mapa
            document.getElementById('btn-deshacer').addEventListener('click', function() {
                var waypoints = routingControl.getWaypoints().filter(wp => wp.latLng !== null);
                if (waypoints.length > 0) {
                    waypoints.pop();
                    routingControl.setWaypoints(waypoints);
                    if(waypoints.length < 2) @this.set('coordenadas_geojson', '');
                }
            });

            document.getElementById('btn-limpiar').addEventListener('click', function() {
                routingControl.setWaypoints([]);
                @this.set('coordenadas_geojson', '');
            });

            // ----------------------------------------------------
            // ESCUCHADORES DE EVENTOS LIVEWIRE PARA CAMBIAR EL MAPA
            // ----------------------------------------------------

            Livewire.on('activar-modo-crear', () => {
                geojsonLayer.clearLayers(); // Ocultar visor
                routingControl.addTo(map);  // Activar dibujado
                routingControl.setWaypoints([]); // Empezar limpio
                document.getElementById('controles-mapa-crear').style.display = 'flex';
                map.setView([-19.5836, -65.7531], 14); // Resetear vista central
            });

            Livewire.on('activar-modo-lista', () => {
                map.removeControl(routingControl); // Desactivar dibujado
                geojsonLayer.clearLayers(); // Limpiar visor
                document.getElementById('controles-mapa-crear').style.display = 'none';
                map.setView([-19.5836, -65.7531], 14);
            });

            Livewire.on('dibujar-ruta-visor', (event) => {
                // Al hacer clic en una tarjeta, se dibuja aquí
                map.removeControl(routingControl); 
                geojsonLayer.clearLayers(); 

                var data = JSON.parse(event.geojson);
                geojsonLayer.addData(data);
                
                // Hacer zoom automático para que la ruta quepa en la pantalla
                map.fitBounds(geojsonLayer.getBounds(), { padding: [40, 40] });
            });
            
            // Estado inicial basado en la variable de Livewire
            if ("{{ $modo }}" === 'crear') {
                routingControl.addTo(map);
                document.getElementById('controles-mapa-crear').style.display = 'flex'; // <- ESTO FALTABA
            }
        });
    </script>
</div>