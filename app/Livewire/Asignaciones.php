<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AsignacionRutaModel;
use App\Models\DetalleCuadrillaModel;
use App\Models\CamionModel;
use App\Models\RutaModel;
use App\Models\UsuarioModel;
use App\Models\ContingenciaParoModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Asignaciones extends Component
{
    use WithPagination;

    public $search = '';

    public $showModal = false;
    public $showAsistenciaModal = false;

    public $fecha;
    public $turno = '';
    public $id_ruta = '';
    public $id_camion = '';
    public $cuadrilla = []; 
    public $trabajador_seleccionado = '';

    public $asignacion_id_asistencia = '';
    public $datos_asignacion = null;
    public $asistencias = [];

    public function mount()
    {
        $this->fecha = Carbon::tomorrow()->toDateString();
    }

    public function render()
    {
        $search = mb_strtolower(trim($this->search));

        $asignaciones = AsignacionRutaModel::with(['camion', 'ruta', 'detallesCuadrilla.usuario'])
            ->whereHas('ruta', function($q) use ($search) {
                $q->whereRaw('LOWER(nombre_ruta) LIKE ?', ["%{$search}%"]);
            })
            ->orderBy('fecha', 'desc')
            ->orderBy('id_asignacion', 'desc')
            ->paginate(10);

        $rutas = RutaModel::with('zona')->orderBy('nombre_ruta', 'asc')->get();
        $camiones = CamionModel::where('estado_operativo', 'Operativo')->get();
        $personal = UsuarioModel::where('estado', 'Activo')
                        ->where('cargo_base', '!=', 'Administrador')
                        ->orderBy('nombre_completo', 'asc')
                        ->get();

        return view('livewire.asignaciones', compact('asignaciones', 'rutas', 'camiones', 'personal'));
    }

    public function updatedIdRuta($value)
    {
        if ($value) {
            $ruta = RutaModel::find($value);
            $this->turno = $ruta ? $ruta->horario_permitido : '';
        } else {
            $this->turno = '';
        }
    }

    public function openModal()
    {
        $this->reset(['turno', 'id_ruta', 'id_camion', 'cuadrilla', 'trabajador_seleccionado']);
        $this->fecha = Carbon::tomorrow()->toDateString();
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
    }

    public function agregarTrabajador()
    {
        $this->validate(['trabajador_seleccionado' => 'required'], ['trabajador_seleccionado.required' => 'Selecciona a un trabajador.']);

        $usuario = UsuarioModel::find($this->trabajador_seleccionado);
        $rol_automatico = $usuario->cargo_base;

        foreach ($this->cuadrilla as $miembro) {
            if ($miembro['id_usuario'] == $this->trabajador_seleccionado) {
                $this->addError('trabajador_seleccionado', 'Este trabajador ya está en la cuadrilla.');
                return;
            }
        }

        $yaAsignado = DetalleCuadrillaModel::where('id_usuario', $this->trabajador_seleccionado)
            ->whereHas('asignacion', function($q) {
                $q->where('fecha', $this->fecha);
            })->exists();

        if ($yaAsignado) {
            $this->addError('trabajador_seleccionado', '¡Alerta! Este personal ya está asignado a otra ruta en esta misma fecha.');
            return;
        }

        if ($rol_automatico === 'Chofer') {
            foreach ($this->cuadrilla as $miembro) {
                if ($miembro['rol_en_viaje'] === 'Chofer') {
                    $this->addError('trabajador_seleccionado', 'Ya hay un Chofer asignado a este camión.');
                    return;
                }
            }
        }

        $this->cuadrilla[] = [
            'id_usuario' => $usuario->id_usuario,
            'nombre' => $usuario->nombre_completo,
            'rol_en_viaje' => $rol_automatico
        ];
        $this->trabajador_seleccionado = '';
    }

    public function quitarTrabajador($index)
    {
        unset($this->cuadrilla[$index]);
        $this->cuadrilla = array_values($this->cuadrilla);
    }

    public function guardarPlanificacion()
    {
        $this->validate([
            'fecha' => 'required|date|after_or_equal:today',
            'turno' => 'required',
            'id_ruta' => 'required',
            'id_camion' => 'required',
        ], [
            'fecha.after_or_equal' => 'No puedes programar rutas en fechas que ya pasaron.'
        ]);

        $contingencia = ContingenciaParoModel::where('fecha', $this->fecha)->first();
        if ($contingencia && $contingencia->bloqueo_total) {
            $this->addError('fecha', 'OPERACIÓN DENEGADA: Hay un bloqueo total programado (' . $contingencia->descripcion . ').');
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Rutas bloqueadas por contingencia']);
            return;
        }

        $camionActual = CamionModel::find($this->id_camion);
        if ($camionActual && $camionActual->estado_operativo !== 'Operativo') {
            $this->addError('id_camion', 'Error: Este vehículo está "' . $camionActual->estado_operativo . '" y no puede ser despachado.');
            return;
        }

        $camionOcupado = AsignacionRutaModel::where('id_camion', $this->id_camion)
                            ->where('fecha', $this->fecha)
                            ->where('turno', $this->turno)
                            ->exists();

        if ($camionOcupado) {
            $this->addError('id_camion', 'Este vehículo ya está asignado a otra ruta en este turno.');
            return;
        }

        if (count($this->cuadrilla) === 0) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'La cuadrilla no puede estar vacía.']);
            return;
        }

        $tieneChofer = false;
        foreach ($this->cuadrilla as $miembro) {
            if ($miembro['rol_en_viaje'] === 'Chofer') {
                $tieneChofer = true;
                break;
            }
        }
        if (!$tieneChofer) {
            $this->dispatch('toast', ['icon' => 'warning', 'title' => 'Operación inválida: El camión necesita al menos un Chofer.']);
            return;
        }

        DB::beginTransaction();
        try {
            $asignacion = AsignacionRutaModel::create([
                'id_camion' => $this->id_camion,
                'id_ruta' => $this->id_ruta,
                'fecha' => $this->fecha,
                'turno' => $this->turno,
                'estado_operacion' => 'Programada'
            ]);

            foreach ($this->cuadrilla as $miembro) {
                DetalleCuadrillaModel::create([
                    'id_asignacion' => $asignacion->id_asignacion,
                    'id_usuario' => $miembro['id_usuario'],
                    'rol_en_viaje' => $miembro['rol_en_viaje'],
                    'asistio' => false 
                ]);
            }
            DB::commit();
            $this->closeModal();
            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Asignación programada exitosamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function abrirAsistencia($id)
    {
        $this->datos_asignacion = AsignacionRutaModel::with('detallesCuadrilla.usuario', 'ruta')->findOrFail($id);
        $this->asignacion_id_asistencia = $id;
        
        $this->asistencias = [];
        foreach($this->datos_asignacion->detallesCuadrilla as $detalle) {
            $this->asistencias[$detalle->id_usuario] = (bool) $detalle->asistio;
        }

        $this->showAsistenciaModal = true;
    }

    public function closeAsistenciaModal()
    {
        $this->showAsistenciaModal = false;
        $this->datos_asignacion = null;
    }

    public function guardarAsistencia()
    {
        DB::beginTransaction();
        try {
            foreach($this->asistencias as $id_usuario => $asistio) {
                $detalle = DetalleCuadrillaModel::where('id_asignacion', $this->asignacion_id_asistencia)
                            ->where('id_usuario', $id_usuario)->first();
                
                if($detalle) {
                    $nueva_hora_marcaje = $detalle->hora_marcaje;

                    if($asistio && !$detalle->hora_marcaje) {
                        $nueva_hora_marcaje = Carbon::now();
                    } elseif (!$asistio) {
                        $nueva_hora_marcaje = null;
                    }

                    DetalleCuadrillaModel::where('id_asignacion', $this->asignacion_id_asistencia)
                        ->where('id_usuario', $id_usuario)
                        ->update([
                            'asistio' => $asistio,
                            'hora_marcaje' => $nueva_hora_marcaje
                        ]);
                }
            }

            $viaje = AsignacionRutaModel::find($this->asignacion_id_asistencia);
            $viaje->estado_operacion = 'En Ruta';
            $viaje->save();

            DB::commit();
            $this->closeAsistenciaModal();
            $this->dispatch('toast', ['icon' => 'success', 'title' => 'Asistencia registrada y viaje Despachado']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'Error: ' . $e->getMessage()]);
        }
    }

    public function eliminar($id)
    {
        $asignacion = AsignacionRutaModel::findOrFail($id);

        if ($asignacion->estado_operacion !== 'Programada') {
            $this->dispatch('toast', [
                'icon' => 'error', 
                'title' => 'Operación denegada: No se puede eliminar una ruta que ya está en curso o finalizada porque afectaría los pagos.'
            ]);
            return;
        }

        $asignacion->delete();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Asignación eliminada del sistema.']);
    }
}
