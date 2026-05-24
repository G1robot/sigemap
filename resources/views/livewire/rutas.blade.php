<div class="px-4 pb-8">
    
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-800 tracking-tight">TRAZADO DE RUTAS DE RECOLECCIÓN</h2>
        <p class="text-sm text-gray-500 mt-1">Diseña los recorridos usando el asistente inteligente.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-gray-100 p-6 h-fit">
            <form wire:submit.prevent="guardarRuta">
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Zona Asignada *</label>
                        <select wire:model="id_zona" class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                            <option value="">Seleccione una zona...</option>
                            @foreach($zonas as $zona)
                                <option value="{{ $zona->id_zona }}">{{ $zona->nombre_zona }}</option>
                            @endforeach
                        </select>
                        @error('id_zona') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nombre de la Ruta *</label>
                        <input type="text" wire:model="nombre_ruta" placeholder="Ej: Ruta Casco Viejo - Norte" 
                            class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500">
                        @error('nombre_ruta') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Horario Operativo *</label>
                        <select wire:model="horario_permitido" class="w-full border border-gray-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white">
                            <option value="">Seleccione un turno...</option>
                            <option value="Mañana (07:00 - 13:00)">Mañana (07:00 - 13:00)</option>
                            <option value="Tarde (13:00 - 19:00)">Tarde (13:00 - 19:00)</option>
                            <option value="Noche (19:00 - 01:00)">Noche (19:00 - 01:00)</option>
                        </select>
                        @error('horario_permitido') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <p class="text-xs text-gray-500 mb-4 bg-orange-50 p-3 rounded-lg border border-orange-100">
                        <i class="fa-solid fa-circle-info text-orange-400 mr-1"></i>
                        Haz clic en el mapa para marcar el inicio, puntos intermedios y el final del recorrido. La ruta se ajustará a las calles automáticamente.
                    </p>
                    @error('coordenadas_geojson') <span class="text-red-500 text-xs mb-3 block text-center font-bold">{{ $message }}</span> @enderror
                    
                    <button type="submit" wire:loading.attr="disabled" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-3 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                        <span wire:loading.remove><i class="fa-solid fa-map-location-dot"></i> Guardar Ruta Espacial</span>
                        <span wire:loading><i class="fa-solid fa-spinner fa-spin"></i> Procesando Topología...</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="lg:col-span-2 flex flex-col gap-3">
            
            <div class="flex justify-end gap-2">
                <button type="button" id="btn-deshacer" class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 font-bold py-1.5 px-4 rounded-lg shadow-sm text-sm transition-all">
                    <i class="fa-solid fa-rotate-left mr-1"></i> Deshacer Último
                </button>
                <button type="button" id="btn-limpiar" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 font-bold py-1.5 px-4 rounded-lg shadow-sm text-sm transition-all">
                    <i class="fa-solid fa-trash-can mr-1"></i> Limpiar Mapa
                </button>
            </div>

            <div wire:ignore class="h-[550px] w-full rounded-xl shadow-sm border-2 border-gray-200 overflow-hidden relative z-0">
                <div id="mapa_rutas" class="h-full w-full"></div>
            </div>
        </div>

    </div>

    <script>
        document.addEventListener('livewire:initialized', () => {
            
            // 1. Inicializar mapa centrado en Potosí
            var map = L.map('mapa_rutas').setView([-19.5836, -65.7531], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© SIG-EMAP Potosí'
            }).addTo(map);

            // 2. Configurar el Motor de Trazado Inteligente
            var control = L.Routing.control({
                waypoints: [],
                routeWhileDragging: true,
                language: 'es',
                show: false, 
                addWaypoints: true,
                fitSelectedRoutes: true,
                lineOptions: {
                    styles: [{color: '#f97316', opacity: 0.8, weight: 6}]
                },
                createMarker: function(i, wp, nWps) {
                    var color = (i === 0) ? 'green' : (i === nWps - 1) ? 'red' : 'blue';
                    return L.marker(wp.latLng, {draggable: true}).bindPopup('Punto ' + (i + 1));
                }
            }).addTo(map);

            // 3. Agregar puntos con clic
            map.on('click', function(e) {
                var waypoints = control.getWaypoints().filter(wp => wp.latLng !== null);
                waypoints.push(L.Routing.waypoint(e.latlng));
                control.setWaypoints(waypoints);
            });

            // 4. Extraer geometría
            control.on('routesfound', function(e) {
                var routes = e.routes;
                var coordinates = routes[0].coordinates; 
                
                var geojson = {
                    "type": "LineString",
                    "coordinates": coordinates.map(c => [c.lng, c.lat])
                };
                
                @this.set('coordenadas_geojson', JSON.stringify(geojson));
            });

            // --- NUEVO: LÓGICA DE LOS BOTONES ---

            // Botón: Deshacer último punto
            document.getElementById('btn-deshacer').addEventListener('click', function() {
                var waypoints = control.getWaypoints().filter(wp => wp.latLng !== null);
                if (waypoints.length > 0) {
                    waypoints.pop(); // Quita el último elemento del array
                    control.setWaypoints(waypoints); // Redibuja el mapa
                    
                    // Si quedan menos de 2 puntos, ya no hay una ruta válida, limpiamos la variable de Livewire
                    if(waypoints.length < 2) {
                        @this.set('coordenadas_geojson', '');
                    }
                }
            });

            // Botón: Limpiar todo el mapa
            document.getElementById('btn-limpiar').addEventListener('click', function() {
                control.setWaypoints([]); // Borra todos los puntos
                @this.set('coordenadas_geojson', ''); // Limpia la variable de Livewire
            });

            // ------------------------------------

            // Escuchar el evento del componente para resetear el mapa después de guardar
            Livewire.on('limpiar-mapa', () => {
                control.setWaypoints([]); 
            });
        });
    </script>
</div>