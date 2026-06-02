<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\UsuarioModel;
use App\Models\DetalleCuadrillaModel;
use App\Models\PagoPersonalModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Planillas extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;
    public $usuario_seleccionado_id = null;

    public $boleta_impresion = null;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $search = mb_strtolower(trim($this->search));

        $personal = UsuarioModel::whereIn('cargo_base', ['Chofer', 'Ayudante'])
            ->whereRaw('LOWER(nombre_completo) LIKE ?', ["%{$search}%"])
            ->orderBy('nombre_completo', 'asc')
            ->paginate(9);

        $liquidaciones = [];

        foreach ($personal as $trabajador) {
            $cantidad_pendientes = DetalleCuadrillaModel::where('id_usuario', $trabajador->id_usuario)
                ->where('asistio', true)
                ->whereNotExists(function($query) use ($trabajador) {
                    $query->select(DB::raw(1))
                          ->from('pagos_personal')
                          ->whereColumn('pagos_personal.id_asignacion', 'detalle_cuadrilla.id_asignacion')
                          ->where('pagos_personal.id_usuario', $trabajador->id_usuario);
                })
                ->count();

            $monto_total_deuda = $cantidad_pendientes * $trabajador->tarifa_por_viaje;

            $liquidaciones[] = [
                'usuario' => $trabajador,
                'cantidad_viajes' => $cantidad_pendientes,
                'tarifa_aplicada' => $trabajador->tarifa_por_viaje,
                'total_a_pagar' => $monto_total_deuda
            ];
        }

        $usuario_detalle = null;
        $viajes_detalle_modal = [];

        if ($this->showModal && $this->usuario_seleccionado_id) {
            $usuario_detalle = UsuarioModel::find($this->usuario_seleccionado_id);
            $viajes_detalle_modal = DetalleCuadrillaModel::where('id_usuario', $this->usuario_seleccionado_id)
                ->where('asistio', true)
                ->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('pagos_personal')
                          ->whereColumn('pagos_personal.id_asignacion', 'detalle_cuadrilla.id_asignacion')
                          ->where('pagos_personal.id_usuario', $this->usuario_seleccionado_id);
                })
                ->with('asignacion.ruta')
                ->get();
        }

        return view('livewire.planillas', compact('personal', 'liquidaciones', 'usuario_detalle', 'viajes_detalle_modal'));
    }

    public function verDetalles($id_usuario)
    {
        $this->usuario_seleccionado_id = $id_usuario;
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->usuario_seleccionado_id = null;
    }

    public function liquidarPago($id_usuario)
    {
        $trabajador = UsuarioModel::findOrFail($id_usuario);

        $viajes_pendientes = DetalleCuadrillaModel::where('id_usuario', $id_usuario)
                ->where('asistio', true)
                ->whereNotExists(function($query) use ($id_usuario) {
                    $query->select(DB::raw(1))
                          ->from('pagos_personal')
                          ->whereColumn('pagos_personal.id_asignacion', 'detalle_cuadrilla.id_asignacion')
                          ->where('pagos_personal.id_usuario', $id_usuario);
                })->get();

        if ($viajes_pendientes->count() === 0) {
            $this->dispatch('toast', ['icon' => 'info', 'title' => 'No hay viajes pendientes.']);
            return;
        }

        DB::beginTransaction();
        try {
            $fecha_pago = Carbon::now();
            $viajes_pagados = 0;
            $monto_total_pagado = 0;

            foreach ($viajes_pendientes as $viaje) {
                PagoPersonalModel::create([
                    'id_usuario' => $id_usuario,
                    'id_asignacion' => $viaje->id_asignacion,
                    'monto_pago' => $trabajador->tarifa_por_viaje,
                    'fecha' => $fecha_pago
                ]);
                $viajes_pagados++;
                $monto_total_pagado += $trabajador->tarifa_por_viaje;
            }

            DB::commit();
            
            $this->boleta_impresion = [
                'nombre' => $trabajador->nombre_completo,
                'ci' => $trabajador->ci,
                'cargo' => $trabajador->cargo_base,
                'fecha_emision' => $fecha_pago->format('d/m/Y H:i'),
                'recibo_nro' => 'EMP-' . str_pad($id_usuario . date('Hs'), 6, '0', STR_PAD_LEFT),
                'cantidad_viajes' => $viajes_pagados,
                'tarifa' => $trabajador->tarifa_por_viaje,
                'total_pagado' => $monto_total_pagado
            ];

            if ($this->usuario_seleccionado_id == $id_usuario) {
                $this->closeModal();
            }

            // $this->dispatch('toast', [
            //     'icon' => 'success', 
            //     'title' => "Liquidación completada. Generando boleta..."
            // ]);

            $this->dispatch('imprimir-boleta');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Error: ' . $e->getMessage()]);
        }
    }
}
