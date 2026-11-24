<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EventosController extends Controller
{
    public function index(Request $request) {
        $inicio = ($request->mes && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $request->mes))
                    ? Carbon::createFromFormat('Y-m-d', $request->mes . '-01')->startOfDay()
                    : Carbon::now()->startOfMonth();

        return view('eventos/index', [
            'semanas' => Evento::calendario($inicio),
            'mes_texto' => $inicio->translatedFormat('F Y'),
            'anterior' => $inicio->copy()->subMonthNoOverflow()->format('Y-m'),
            'siguiente' => $inicio->copy()->addMonthNoOverflow()->format('Y-m'),
            'proximos' => Evento::where('fecha', '>=', now()->toDateString())
                                ->orderBy('fecha')->orderBy('hora')->limit(5)->get(),
        ]);
    }

    public function create(Request $request) {
        $fecha = ($request->fecha && strtotime($request->fecha)) ? date('Y-m-d', strtotime($request->fecha)) : now()->toDateString();

        return view('eventos/create', [ 'fecha' => $fecha, 'recordatorios' => Evento::RECORDATORIOS ]);
    }

    public function edit($id) {
        $evento = Evento::find($id);

        if (empty($evento)) {
            Session::flash('message', [ 'content' => "El evento con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('eventos.index');
        }

        return view('eventos/edit', [ 'evento' => $evento, 'recordatorios' => Evento::RECORDATORIOS ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(), $this->reglas(), $this->mensajes())->validate();

        try {
            $evento = new Evento();
            $this->llenar($evento, $request);
            $evento->save();

            Session::flash('message', [ 'content' => "Evento creado con éxito", 'type' => 'success' ]);
            return redirect()->route('eventos.index', [ 'mes' => substr($request->fecha, 0, 7) ]);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function update(Request $request) {
        Validator::make($request->all(),
            $this->reglas() + [ 'evento_id' => 'required|exists:eventos,id' ],
            $this->mensajes() + [
                'evento_id.required' => 'El evento es requerido',
                'evento_id.exists' => 'El id dado no existe',
            ]
        )->validate();

        try {
            $evento = Evento::find($request->evento_id);
            $this->llenar($evento, $request);
            $evento->save();

            Session::flash('message', [ 'content' => "Evento actualizado con éxito", 'type' => 'success' ]);
            return redirect()->route('eventos.index', [ 'mes' => substr($request->fecha, 0, 7) ]);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $evento = Evento::find($id);

            if (empty($evento)) {
                Session::flash('message', [ 'content' => "El evento con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('eventos.index');
            }

            $mes = $evento->fecha->format('Y-m');
            $evento->delete();

            Session::flash('message', [ 'content' => "Evento eliminado con éxito", 'type' => 'success' ]);
            return redirect()->route('eventos.index', [ 'mes' => $mes ]);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function proximos() {
        return response()->json(Evento::conRecordatorioProximo());
    }

    private function llenar(Evento $evento, Request $request): void {
        $evento->titulo = trim($request->titulo);
        $evento->descripcion = $request->descripcion;
        $evento->fecha = $request->fecha;
        $evento->hora = $request->hora ?: null;
        $evento->recordatorio_min = $request->recordatorio_min ?: null;
    }

    private function reglas(): array {
        return [
            'titulo' => 'required|max:100',
            'descripcion' => 'nullable|max:500',
            'fecha' => 'required|date',
            'hora' => 'nullable|date_format:H:i|required_with:recordatorio_min',
            'recordatorio_min' => ['nullable', 'integer', Rule::in(array_keys(Evento::RECORDATORIOS))],
        ];
    }

    private function mensajes(): array {
        return [
            'titulo.required' => 'El título es requerido',
            'titulo.max' => 'El título no puede ser mayor a :max letras',

            'descripcion.max' => 'La descripción no puede ser mayor a :max letras',

            'fecha.required' => 'La fecha es requerida',
            'fecha.date' => 'La fecha no es válida',

            'hora.date_format' => 'La hora no es válida',
            'hora.required_with' => 'Para usar un recordatorio debes indicar la hora del evento',

            'recordatorio_min.in' => 'El recordatorio no es válido',
        ];
    }
}
