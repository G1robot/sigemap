<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AsignacionRutaModel;
use App\Models\RegistroBotaderoModel;
use App\Models\CamionModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Reportes extends Component
{
    public $fecha_inicio;
    public $fecha_fin;

    public function mount()
    {
        $this->fecha_inicio = Carbon::now()->startOfMonth()->toDateString();
        $this->fecha_fin = Carbon::now()->endOfMonth()->toDateString();
    }

    public function render()
    {
        $inicio = Carbon::parse($this->fecha_inicio)->startOfDay();
        $fin = Carbon::parse($this->fecha_fin)->endOfDay();

        $kpi_toneladas = RegistroBotaderoModel::whereBetween('hora_descarga', [$inicio, $fin])
                            ->sum('peso_descargado_ton');

        $kpi_viajes = AsignacionRutaModel::whereBetween('fecha', [$inicio, $fin])
                            ->where('estado_operacion', 'Finalizada')
                            ->count();

        $kpi_flota = CamionModel::where('estado_operativo', 'Operativo')->count();
        $kpi_flota_total = CamionModel::count();

        $zonasData = RegistroBotaderoModel::join('asignaciones_ruta', 'registro_botadero.id_asignacion', '=', 'asignaciones_ruta.id_asignacion')
            ->join('rutas', 'asignaciones_ruta.id_ruta', '=', 'rutas.id_ruta')
            ->join('zonas', 'rutas.id_zona', '=', 'zonas.id_zona')
            ->select('zonas.nombre_zona', DB::raw('SUM(registro_botadero.peso_descargado_ton) as total_toneladas'))
            ->whereBetween('registro_botadero.hora_descarga', [$inicio, $fin])
            ->groupBy('zonas.nombre_zona')
            ->orderByDesc('total_toneladas')
            ->get();

        $labelsZonas = $zonasData->pluck('nombre_zona');
        $valoresZonas = $zonasData->pluck('total_toneladas');

        $diasData = RegistroBotaderoModel::select(DB::raw('DATE(hora_descarga) as fecha'), DB::raw('SUM(peso_descargado_ton) as total_dia'))
            ->whereBetween('hora_descarga', [$inicio, $fin])
            ->groupBy(DB::raw('DATE(hora_descarga)'))
            ->orderBy('fecha', 'asc')
            ->get();

        $labelsDias = $diasData->map(function ($item) {
            return Carbon::parse($item->fecha)->locale('es')->isoFormat('ddd D MMM');
        });
        $valoresDias = $diasData->pluck('total_dia');

        $this->dispatch('actualizar-graficos', [
            'labelsZonas' => $labelsZonas,
            'valoresZonas' => $valoresZonas,
            'labelsDias' => $labelsDias,
            'valoresDias' => $valoresDias,
        ]);

        return view('livewire.reportes', [
            'kpi_toneladas' => $kpi_toneladas ?? 0,
            'kpi_viajes' => $kpi_viajes,
            'kpi_flota' => $kpi_flota,
            'kpi_flota_total' => $kpi_flota_total,
            'labelsZonas' => $labelsZonas,
            'valoresZonas' => $valoresZonas,
            'labelsDias' => $labelsDias,
            'valoresDias' => $valoresDias,
        ]);
    }
}
