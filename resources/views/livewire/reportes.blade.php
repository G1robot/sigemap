<div class="px-4 pb-10">
    <div class="mb-6 flex flex-col md:flex-row justify-between md:items-end gap-4">
        <div>
            <h2 class="text-2xl font-black text-gray-800 tracking-tight">DASHBOARD GERENCIAL</h2>
            <p class="text-sm text-gray-500 mt-1">Indicadores de rendimiento de recolección de residuos sólidos.</p>
        </div>

        <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-200 flex flex-col sm:flex-row items-center gap-3">
            <div class="flex items-center gap-2 text-emap-blue font-bold px-2">
                <i class="fa-solid fa-calendar-days"></i>
                <span class="text-sm uppercase tracking-wider">Filtro:</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-bold">DESDE</span>
                <input type="date" wire:model.live="fecha_inicio" class="border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emap-gold bg-gray-50 text-gray-700 font-bold">
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400 font-bold">HASTA</span>
                <input type="date" wire:model.live="fecha_fin" class="border border-gray-300 rounded-lg p-2 text-sm focus:ring-2 focus:ring-emap-gold bg-gray-50 text-gray-700 font-bold">
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-5 text-gray-900 text-9xl">
                <i class="fa-solid fa-weight-scale"></i>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-emap-blue/10 text-emap-blue flex items-center justify-center text-3xl z-10">
                <i class="fa-solid fa-dumpster"></i>
            </div>
            <div class="z-10">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Volumen Recolectado</p>
                <h3 class="text-3xl font-black text-gray-800">{{ number_format($kpi_toneladas, 2) }} <span class="text-lg text-gray-400 font-bold">Ton</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-5 text-gray-900 text-9xl">
                <i class="fa-solid fa-route"></i>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-emap-gold/20 text-emap-gold flex items-center justify-center text-3xl z-10">
                <i class="fa-solid fa-truck-fast"></i>
            </div>
            <div class="z-10">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Rutas Completadas</p>
                <h3 class="text-3xl font-black text-gray-800">{{ $kpi_viajes }} <span class="text-lg text-gray-400 font-bold">Viajes</span></h3>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-5 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 opacity-5 text-gray-900 text-9xl">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div class="w-16 h-16 rounded-2xl bg-emap-green/10 text-emap-green flex items-center justify-center text-3xl z-10">
                <i class="fa-solid fa-truck"></i>
            </div>
            <div class="z-10">
                <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Disponibilidad Flota</p>
                <h3 class="text-3xl font-black text-gray-800">{{ $kpi_flota }} <span class="text-lg text-gray-400 font-bold">/ {{ $kpi_flota_total }}</span></h3>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-gray-600 uppercase mb-4 border-b pb-2"><i class="fa-solid fa-chart-line mr-2 text-emap-blue"></i> Tendencia de Recolección (Línea de tiempo)</h3>
            <div wire:ignore class="relative h-72 w-full">
                <canvas id="graficoTendencia"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <h3 class="text-sm font-black text-gray-600 uppercase mb-4 border-b pb-2"><i class="fa-solid fa-chart-pie mr-2 text-emap-gold"></i> Toneladas Recolectadas por Zona</h3>
            <div wire:ignore class="relative h-72 w-full">
                <canvas id="graficoZonas"></canvas>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Declaramos las variables de los gráficos globalmente
        let chartTendencia = null;
        let chartZonas = null;

        document.addEventListener('livewire:initialized', () => {
            const emapBlue = '#003a78';
            const emapGold = '#c29b40';

            // 1. INICIALIZAR GRÁFICO DE TENDENCIA
            const ctxTendencia = document.getElementById('graficoTendencia').getContext('2d');
            chartTendencia = new Chart(ctxTendencia, {
                type: 'line',
                data: {
                    labels: {!! json_encode($labelsDias) !!},
                    datasets: [{
                        label: 'Toneladas Recolectadas',
                        data: {!! json_encode($valoresDias) !!},
                        borderColor: emapBlue,
                        backgroundColor: 'rgba(0, 58, 120, 0.1)',
                        borderWidth: 3,
                        pointBackgroundColor: emapGold,
                        pointRadius: 5,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 2. INICIALIZAR GRÁFICO DE ZONAS
            const ctxZonas = document.getElementById('graficoZonas').getContext('2d');
            chartZonas = new Chart(ctxZonas, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($labelsZonas) !!},
                    datasets: [{
                        label: 'Toneladas Totales',
                        data: {!! json_encode($valoresZonas) !!},
                        backgroundColor: emapGold,
                        borderRadius: 6,
                        barPercentage: 0.6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                        x: { grid: { display: false } }
                    }
                }
            });

            // 3. ESCUCHAR LOS CAMBIOS DE FECHA DESDE LIVEWIRE
            Livewire.on('actualizar-graficos', (event) => {
                const newData = event[0]; // Capturamos los datos enviados por PHP

                // Actualizamos el Gráfico de Tendencia
                chartTendencia.data.labels = newData.labelsDias;
                chartTendencia.data.datasets[0].data = newData.valoresDias;
                chartTendencia.update(); // Hace la animación sola

                // Actualizamos el Gráfico de Zonas
                chartZonas.data.labels = newData.labelsZonas;
                chartZonas.data.datasets[0].data = newData.valoresZonas;
                chartZonas.update(); // Hace la animación sola
            });
        });
    </script>
</div>