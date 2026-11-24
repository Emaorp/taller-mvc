<?php

namespace App\Http\Controllers;

use App\Models\Memoria;
use App\Models\PartidaMemoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MemoriaController extends Controller
{
    public function index() {
        $ranking = [];

        foreach (Memoria::NIVELES as $clave => $nivel) {
            $ranking[$clave] = PartidaMemoria::where('nivel', $clave)
                                             ->orderByDesc('puntaje')->orderBy('segundos')
                                             ->limit(5)->get();
        }

        return view('memoria/index', [ 'niveles' => Memoria::NIVELES, 'ranking' => $ranking ]);
    }

    public function jugar($nivel) {
        if (!Memoria::nivelValido($nivel)) {
            Session::flash('message', [ 'content' => "El nivel '$nivel' no existe.", 'type' => 'error' ]);
            return redirect()->route('memoria.index');
        }

        return view('memoria/jugar', [
            'clave' => $nivel,
            'nivel' => Memoria::NIVELES[$nivel],
            'baraja' => Memoria::generarBaraja($nivel),
        ]);
    }

    public function guardar(Request $request) {
        Validator::make($request->all(),
        [
            'jugador' => 'required|max:40',
            'nivel' => ['required', Rule::in(array_keys(Memoria::NIVELES))],
            'movimientos' => 'required|integer|min:1|max:1000',
            'segundos' => 'required|integer|min:1|max:86400',
        ],
        [
            'jugador.required' => 'El nombre del jugador es requerido',
            'jugador.max' => 'El nombre no puede ser mayor a :max letras',

            'nivel.required' => 'El nivel es requerido',
            'nivel.in' => 'El nivel no es válido',

            'movimientos.required' => 'Los movimientos son requeridos',
            'movimientos.integer' => 'Los movimientos deben ser un número entero',
            'movimientos.min' => 'Los movimientos deben ser al menos :min',
            'movimientos.max' => 'Los movimientos no pueden ser más de :max',

            'segundos.required' => 'El tiempo es requerido',
            'segundos.integer' => 'El tiempo debe ser un número entero de segundos',
            'segundos.min' => 'El tiempo debe ser al menos :min segundo',
            'segundos.max' => 'El tiempo es demasiado grande',
        ])->validate();

        if ($request->movimientos < Memoria::NIVELES[$request->nivel]['pares']) {
            Session::flash('message', [ 'content' => "La partida no es válida.", 'type' => 'error' ]);
            return redirect()->route('memoria.index');
        }

        try {
            $puntaje = Memoria::calcularPuntaje($request->nivel, (int) $request->movimientos, (int) $request->segundos);

            $partida = new PartidaMemoria();
            $partida->jugador = trim($request->jugador);
            $partida->nivel = $request->nivel;
            $partida->movimientos = $request->movimientos;
            $partida->segundos = $request->segundos;
            $partida->puntaje = $puntaje;
            $partida->save();

            Session::flash('message', [ 'content' => "¡Puntaje guardado: $puntaje puntos!", 'type' => 'success' ]);
            return redirect()->route('memoria.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }
}
