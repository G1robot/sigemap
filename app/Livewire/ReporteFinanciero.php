<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\PagoPersonalModel;
use Carbon\Carbon;

class ReporteFinanciero extends Component
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

        $pagos_brutos = PagoPersonalModel::with(['usuario', 'asignacion.ruta'])
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('fecha', 'desc')
            ->get();

        $kpi_total_pagado = $pagos_brutos->sum('monto_pago');
        $kpi_viajes_pagados = $pagos_brutos->count();
        
        $pagos_agrupados = $pagos_brutos->groupBy('id_usuario');
        $kpi_trabajadores = $pagos_agrupados->count();

        $reporte_detallado = [];
        foreach ($pagos_agrupados as $id_usuario => $lista_pagos) {
            $primer_pago = $lista_pagos->first(); 
            
            $reporte_detallado[] = [
                'usuario' => $primer_pago->usuario,
                'total_ganado' => $lista_pagos->sum('monto_pago'),
                'cantidad_rutas' => $lista_pagos->count(),
                'desglose' => $lista_pagos 
            ];
        }

        usort($reporte_detallado, function($a, $b) {
            return strcmp($a['usuario']->nombre_completo, $b['usuario']->nombre_completo);
        });

        return view('livewire.reporte-financiero', compact(
            'reporte_detallado', 
            'kpi_total_pagado', 
            'kpi_viajes_pagados', 
            'kpi_trabajadores',
            'inicio',
            'fin'
        ));
    }
}
