<?php

namespace App\Http\Controllers;

use App\Models\Receta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RecetasController extends Controller
{
    public function index(Request $request) {
        $consulta = Receta::buscar($request->filter);

        if (in_array($request->tipo_comida, Receta::TIPOS)) {
            $consulta->where('tipo_comida', $request->tipo_comida);
        }

        if (!empty($request->ingrediente)) {
            $consulta->where('ingredientes', 'LIKE', "%$request->ingrediente%");
        }

        if ($request->boolean('solo_guardadas')) {
            $consulta->where('guardada', true);
        }

        return view('recetas/index', [
            'recetas' => $consulta->orderByDesc('id')->paginate(9)->withQueryString(),
            'tipos' => Receta::TIPOS,
            'data' => $request,
        ]);
    }

    public function create() {
        return view('recetas/create', [ 'tipos' => Receta::TIPOS ]);
    }

    public function edit($id) {
        $receta = Receta::find($id);

        if (empty($receta)) {
            Session::flash('message', [ 'content' => "La receta con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('recetas.index');
        }

        return view('recetas/edit', [ 'receta' => $receta, 'tipos' => Receta::TIPOS ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(), $this->reglas(), $this->mensajes())->validate();

        try {
            $receta = new Receta();
            $this->llenar($receta, $request);
            $receta->save();

            Session::flash('message', [ 'content' => "Receta creada con éxito", 'type' => 'success' ]);
            return redirect()->route('recetas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function update(Request $request) {
        Validator::make($request->all(),
            $this->reglas() + [ 'receta_id' => 'required|exists:recetas,id' ],
            $this->mensajes() + [
                'receta_id.required' => 'La receta es requerida',
                'receta_id.exists' => 'El id dado no existe',
            ]
        )->validate();

        try {
            $receta = Receta::find($request->receta_id);
            $this->llenar($receta, $request);
            $receta->save();

            Session::flash('message', [ 'content' => "Receta actualizada con éxito", 'type' => 'success' ]);
            return redirect()->route('recetas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $receta = Receta::find($id);

            if (empty($receta)) {
                Session::flash('message', [ 'content' => "La receta con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('recetas.index');
            }

            $receta->delete();

            Session::flash('message', [ 'content' => "Receta eliminada con éxito", 'type' => 'success' ]);
            return redirect()->route('recetas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function guardar($id) {
        try {
            $receta = Receta::find($id);

            if (empty($receta)) {
                Session::flash('message', [ 'content' => "La receta con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('recetas.index');
            }

            $receta->guardada = !$receta->guardada;
            $receta->save();

            Session::flash('message', [
                'content' => $receta->guardada ? "Receta guardada en tus favoritas" : "Receta quitada de tus favoritas",
                'type' => 'success',
            ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function compartida($token) {
        $receta = Receta::where('token', $token)->first();

        if (empty($receta)) {
            abort(404, 'La receta compartida no existe.');
        }

        return view('recetas/show', [ 'receta' => $receta ]);
    }

    private function llenar(Receta $receta, Request $request): void {
        $receta->titulo = trim($request->titulo);
        $receta->tipo_comida = $request->tipo_comida;
        $receta->ingredientes = trim($request->ingredientes);
        $receta->preparacion = trim($request->preparacion);
        $receta->tiempo_min = $request->tiempo_min;
        $receta->porciones = $request->porciones;
    }

    private function reglas(): array {
        return [
            'titulo' => 'required|max:120',
            'tipo_comida' => ['required', Rule::in(Receta::TIPOS)],
            'ingredientes' => 'required|max:3000',
            'preparacion' => 'required|max:5000',
            'tiempo_min' => 'required|integer|min:1|max:1440',
            'porciones' => 'required|integer|min:1|max:50',
        ];
    }

    private function mensajes(): array {
        return [
            'titulo.required' => 'El título es requerido',
            'titulo.max' => 'El título no puede ser mayor a :max letras',

            'tipo_comida.required' => 'El tipo de comida es requerido',
            'tipo_comida.in' => 'El tipo de comida no es válido',

            'ingredientes.required' => 'Los ingredientes son requeridos',
            'ingredientes.max' => 'Los ingredientes no pueden ser mayores a :max letras',

            'preparacion.required' => 'La preparación es requerida',
            'preparacion.max' => 'La preparación no puede ser mayor a :max letras',

            'tiempo_min.required' => 'El tiempo de preparación es requerido',
            'tiempo_min.integer' => 'El tiempo debe ser un número entero de minutos',
            'tiempo_min.min' => 'El tiempo debe ser de al menos :min minuto',
            'tiempo_min.max' => 'El tiempo no puede ser mayor a :max minutos',

            'porciones.required' => 'Las porciones son requeridas',
            'porciones.integer' => 'Las porciones deben ser un número entero',
            'porciones.min' => 'Debe haber al menos :min porción',
            'porciones.max' => 'No pueden ser más de :max porciones',
        ];
    }
}
