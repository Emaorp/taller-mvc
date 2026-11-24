<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Models\Servicio;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ReservasController extends Controller
{
    public function index(Request $request) {
        $consulta = Reserva::with('servicio');

        if (in_array($request->estado, [Reserva::PENDIENTE, Reserva::CONFIRMADA, Reserva::CANCELADA])) {
            $consulta->where('estado', $request->estado);
        }

        if (!empty($request->filter)) {
            $consulta->where(function ($q) use ($request) {
                $q->where('nombre', 'LIKE', "%$request->filter%")
                  ->orWhere('email', 'LIKE', "%$request->filter%");
            });
        }

        $reservas = $consulta->orderByDesc('fecha')->orderBy('hora')->paginate(15)->withQueryString();

        return view('reservas/index', [ 'reservas' => $reservas, 'data' => $request ]);
    }

    public function create(Request $request) {
        $servicios = Servicio::orderBy('nombre')->get();

        $servicioId = (int) ($request->servicio_id ?: $servicios->first()?->id);
        $fecha = $request->fecha;

        if (empty($fecha) || !strtotime($fecha) || $fecha < now()->toDateString()) {
            $fecha = now()->toDateString();
        }

        $disponibilidad = $servicioId ? Reserva::disponibilidad($servicioId, $fecha) : [];

        return view('reservas/create', [
            'servicios' => $servicios,
            'servicio_id' => $servicioId,
            'fecha' => $fecha,
            'disponibilidad' => $disponibilidad,
        ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(),
        [
            'servicio_id' => 'required|exists:servicios,id',
            'nombre' => 'required|max:80',
            'email' => 'required|email|max:100',
            'fecha' => 'required|date|after_or_equal:today',
            'hora' => ['required', Rule::in(Reserva::HORAS)],
        ],
        [
            'servicio_id.required' => 'El servicio es requerido',
            'servicio_id.exists' => 'El servicio no existe',

            'nombre.required' => 'El nombre es requerido',
            'nombre.max' => 'El nombre no puede ser mayor a :max letras',

            'email.required' => 'El correo es requerido',
            'email.email' => 'El correo no es válido',
            'email.max' => 'El correo no puede ser mayor a :max letras',

            'fecha.required' => 'La fecha es requerida',
            'fecha.date' => 'La fecha no es válida',
            'fecha.after_or_equal' => 'La fecha no puede ser anterior a hoy',

            'hora.required' => 'Selecciona una hora disponible',
            'hora.in' => 'La hora seleccionada no es válida',
        ])->validate();

        $disponibilidad = Reserva::disponibilidad((int) $request->servicio_id, $request->fecha);

        if (empty($disponibilidad[$request->hora])) {
            Session::flash('message', [ 'content' => "Esa hora ya no está disponible. Elige otra.", 'type' => 'error' ]);
            return redirect()->route('reservas.create', [ 'servicio_id' => $request->servicio_id, 'fecha' => $request->fecha ]);
        }

        try {
            $reserva = new Reserva();
            $reserva->servicio_id = $request->servicio_id;
            $reserva->nombre = trim($request->nombre);
            $reserva->email = trim($request->email);
            $reserva->fecha = $request->fecha;
            $reserva->hora = $request->hora;
            $reserva->estado = Reserva::PENDIENTE;
            $reserva->save();

            Session::flash('message', [ 'content' => "Reserva creada. Queda pendiente de confirmación.", 'type' => 'success' ]);
            return redirect()->route('reservas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function confirm($id) {
        return $this->cambiarEstado($id, Reserva::CONFIRMADA, [Reserva::PENDIENTE], 'Reserva confirmada con éxito');
    }

    public function cancel($id) {
        return $this->cambiarEstado($id, Reserva::CANCELADA, [Reserva::PENDIENTE, Reserva::CONFIRMADA], 'Reserva cancelada con éxito');
    }

    public function delete($id) {
        try {
            $reserva = Reserva::find($id);

            if (empty($reserva)) {
                Session::flash('message', [ 'content' => "La reserva con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('reservas.index');
            }

            $reserva->delete();

            Session::flash('message', [ 'content' => "Reserva eliminada con éxito", 'type' => 'success' ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    private function cambiarEstado($id, string $nuevo, array $permitidos, string $mensaje) {
        try {
            $reserva = Reserva::find($id);

            if (empty($reserva)) {
                Session::flash('message', [ 'content' => "La reserva con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('reservas.index');
            }

            if (!in_array($reserva->estado, $permitidos)) {
                Session::flash('message', [ 'content' => "La reserva está {$reserva->estado} y no se puede cambiar a $nuevo.", 'type' => 'error' ]);
                return redirect()->back();
            }

            $reserva->estado = $nuevo;
            $reserva->save();

            Session::flash('message', [ 'content' => $mensaje, 'type' => 'success' ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }
}
