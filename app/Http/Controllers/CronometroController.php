<?php

namespace App\Http\Controllers;

use App\Models\SesionCronometro;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CronometroController extends Controller
{
    public function index() {
        return view('cronometro/index', [
            'sesiones' => SesionCronometro::orderByDesc('id')->limit(10)->get(),
        ]);
    }

    public function store(Request $request) {
        $validador = Validator::make($request->all(),
        [
            'nombre' => 'required|max:60',
            'duracion_ms' => 'required|integer|min:1|max:864000000',
            'vueltas' => 'nullable|json',
        ],
        [
            'nombre.required' => 'El nombre de la sesión es requerido',
            'nombre.max' => 'El nombre no puede ser mayor a :max letras',

            'duracion_ms.required' => 'Primero toma un tiempo con el cronómetro',
            'duracion_ms.integer' => 'La duración no es válida',
            'duracion_ms.min' => 'Primero toma un tiempo con el cronómetro',
            'duracion_ms.max' => 'La duración es demasiado grande',

            'vueltas.json' => 'Las vueltas no son válidas',
        ]);

        $vueltas = json_decode($request->vueltas ?: '[]', true);

        $validador->after(function ($v) use ($vueltas) {
            if (!is_array($vueltas) || count($vueltas) > 500 || collect($vueltas)->contains(fn ($x) => !is_int($x) || $x < 0)) {
                $v->errors()->add('vueltas', 'Las vueltas no son válidas');
            }
        });

        $validador->validate();

        try {
            $sesion = new SesionCronometro();
            $sesion->nombre = trim($request->nombre);
            $sesion->duracion_ms = $request->duracion_ms;
            $sesion->vueltas = $vueltas;
            $sesion->save();

            Session::flash('message', [ 'content' => "Sesión guardada con éxito", 'type' => 'success' ]);
            return redirect()->route('cronometro.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $sesion = SesionCronometro::find($id);

            if (empty($sesion)) {
                Session::flash('message', [ 'content' => "La sesión con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('cronometro.index');
            }

            $sesion->delete();

            Session::flash('message', [ 'content' => "Sesión eliminada con éxito", 'type' => 'success' ]);
            return redirect()->route('cronometro.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }
}
