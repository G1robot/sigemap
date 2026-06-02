<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\CamionModel;
use App\Models\AsignacionRutaModel;
use App\Models\RegistroBotaderoModel;
use App\Models\HistorialMantenimientoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReporteCamiones extends Component
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

        $camiones = CamionModel::with(['historialMantenimiento' => function($query) use ($inicio, $fin) {
            $query->whereBetween('fecha_ingreso', [$inicio, $fin])
                  ->orderBy('fecha_ingreso', 'desc');
        }])->orderBy('placa', 'asc')->get();

        foreach ($camiones as $camion) {
            
            $camion->viajes_completados = AsignacionRutaModel::where('id_camion', $camion->id_camion)
                ->whereBetween('fecha', [$inicio, $fin])
                ->where('estado_operacion', 'Finalizada')
                ->count();

            $camion->toneladas_recolectadas = RegistroBotaderoModel::whereHas('asignacion', function($query) use ($camion, $inicio, $fin) {
                $query->where('id_camion', $camion->id_camion)
                      ->whereBetween('fecha', [$inicio, $fin])
                      ->where('estado_operacion', 'Finalizada');
            })->sum('peso_descargado_ton');
        }

        return view('livewire.reporte-camiones', compact('camiones', 'inicio', 'fin'));
    }
}
