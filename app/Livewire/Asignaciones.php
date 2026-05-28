<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\AsignacionRutaModel;
use App\Models\DetalleCuadrillaModel;
use App\Models\CamionModel;
use App\Models\RutaModel;
use App\Models\UsuarioModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class Asignaciones extends Component
{
    use WithPagination;

    public $search = '';

    // Controles de Modales
    public $showModal = false;
    public $showAsistenciaModal = false;

    // --- VARIABLES PARA CREACIÓN ---
    public $fecha;
    public $turno = '';
    public $id_ruta = '';
    public $id_camion = '';
    public $cuadrilla = []; 
    public $trabajador_seleccionado = '';

    // --- VARIABLES PARA ASISTENCIA ---
    public $asignacion_id_asistencia = '';
    public $datos_asignacion = null;
    public $asistencias = []; // Array reactivo para los checkboxes [id_usuario => boolean]

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

        $rutas = RutaModel::orderBy('nombre_ruta', 'asc')->get();
        $camiones = CamionModel::where('estado_operativo', 'Operativo')->get();
        $personal = UsuarioModel::where('estado', 'Activo')
                        ->where('cargo_base', '!=', 'Administrador')
                        ->orderBy('nombre_completo', 'asc')
                        ->get();

        return view('livewire.asignaciones', compact('asignaciones', 'rutas', 'camiones', 'personal'));
    }

    // --- MAGIA: AUTOCOMPLETAR TURNO ---
    public function updatedIdRuta($value)
    {
        if ($value) {
            $ruta = RutaModel::find($value);
            $this->turno = $ruta ? $ruta->horario_permitido : '';
        } else {
            $this->turno = '';
        }
    }

    // --- LÓGICA DE CREACIÓN (MODAL 1) ---
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

        // 1. Evitar duplicados en la lista temporal
        foreach ($this->cuadrilla as $miembro) {
            if ($miembro['id_usuario'] == $this->trabajador_seleccionado) {
                $this->addError('trabajador_seleccionado', 'Este trabajador ya está en la cuadrilla.');
                return;
            }
        }

        // 2. REGLA DE ORO: Verificar si el trabajador ya tiene ruta ese mismo día en la BD
        $yaAsignado = DetalleCuadrillaModel::where('id_usuario', $this->trabajador_seleccionado)
            ->whereHas('asignacion', function($q) {
                $q->where('fecha', $this->fecha);
            })->exists();

        if ($yaAsignado) {
            $this->addError('trabajador_seleccionado', '¡Alerta! Este personal ya está asignado a otra ruta en esta misma fecha.');
            return;
        }

        // 3. Evitar doble Chofer
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
            'fecha' => 'required|date',
            'turno' => 'required',
            'id_ruta' => 'required',
            'id_camion' => 'required',
        ]);

        if (count($this->cuadrilla) === 0) {
            $this->dispatch('toast', ['icon' => 'error', 'title' => 'La cuadrilla no puede estar vacía.']);
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

    // --- LÓGICA DE ASISTENCIA (MODAL 2) ---
    public function abrirAsistencia($id)
    {
        $this->datos_asignacion = AsignacionRutaModel::with('detallesCuadrilla.usuario', 'ruta')->findOrFail($id);
        $this->asignacion_id_asistencia = $id;
        
        // Llenar el array reactivo con el estado actual
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
                // Buscamos el registro actual para saber si ya tenía hora de marcaje
                $detalle = DetalleCuadrillaModel::where('id_asignacion', $this->asignacion_id_asistencia)
                            ->where('id_usuario', $id_usuario)->first();
                
                if($detalle) {
                    $nueva_hora_marcaje = $detalle->hora_marcaje;

                    // Lógica de la hora
                    if($asistio && !$detalle->hora_marcaje) {
                        $nueva_hora_marcaje = Carbon::now();
                    } elseif (!$asistio) {
                        $nueva_hora_marcaje = null;
                    }

                    // SOLUCIÓN: Usamos update() directamente con los wheres correspondientes
                    DetalleCuadrillaModel::where('id_asignacion', $this->asignacion_id_asistencia)
                        ->where('id_usuario', $id_usuario)
                        ->update([
                            'asistio' => $asistio,
                            'hora_marcaje' => $nueva_hora_marcaje
                        ]);
                }
            }

            // Cambiamos el estado del viaje (Esta tabla sí tiene id normal, así que save() funciona)
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
        AsignacionRutaModel::findOrFail($id)->delete();
        $this->dispatch('toast', ['icon' => 'success', 'title' => 'Asignación eliminada']);
    }
}
