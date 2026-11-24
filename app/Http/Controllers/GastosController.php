<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use App\Models\Gasto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class GastosController extends Controller
{
    public function index(Request $request) {
        $inicio = Gasto::inicioDeMes($request->mes);

        $consulta = Gasto::delMes($inicio)->with('categoria');

        if (!empty($request->categoria_id)) {
            $consulta->where('categoria_gasto_id', $request->categoria_id);
        }

        $gastos = $consulta->orderByDesc('fecha')->orderByDesc('id')->paginate(15)->withQueryString();

        return view('gastos/index', [
            'gastos' => $gastos,
            'categorias' => CategoriaGasto::orderBy('nombre')->get(),
            'mes' => $inicio->format('Y-m'),
            'categoria_id' => $request->categoria_id,
            'total' => (float) Gasto::delMes($inicio)->sum('monto'),
        ]);
    }

    public function resumen(Request $request) {
        $inicio = Gasto::inicioDeMes($request->mes);

        return view('gastos/resumen', [
            'mes' => $inicio->format('Y-m'),
            'mes_texto' => $inicio->translatedFormat('F Y'),
            'resumen' => Gasto::resumenMensual($inicio),
            'historico' => Gasto::totalesUltimosMeses($inicio, 6),
        ]);
    }

    public function create() {
        return view('gastos/create', [ 'categorias' => CategoriaGasto::orderBy('nombre')->get() ]);
    }

    public function edit($id) {
        $gasto = Gasto::find($id);

        if (empty($gasto)) {
            Session::flash('message', [ 'content' => "El gasto con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('gastos.index');
        }

        return view('gastos/edit', [
            'gasto' => $gasto,
            'categorias' => CategoriaGasto::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(), $this->reglas(), $this->mensajes())->validate();

        try {
            $gasto = new Gasto();
            $gasto->descripcion = trim($request->descripcion);
            $gasto->monto = $request->monto;
            $gasto->fecha = $request->fecha;
            $gasto->categoria_gasto_id = $request->categoria_gasto_id;
            $gasto->save();

            Session::flash('message', [ 'content' => "Gasto registrado con éxito", 'type' => 'success' ]);
            return redirect()->route('gastos.index', [ 'mes' => substr($request->fecha, 0, 7) ]);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function update(Request $request) {
        Validator::make($request->all(),
            $this->reglas() + [ 'gasto_id' => 'required|exists:gastos,id' ],
            $this->mensajes() + [
                'gasto_id.required' => 'El gasto es requerido',
                'gasto_id.exists' => 'El id dado no existe',
            ]
        )->validate();

        try {
            $gasto = Gasto::find($request->gasto_id);
            $gasto->descripcion = trim($request->descripcion);
            $gasto->monto = $request->monto;
            $gasto->fecha = $request->fecha;
            $gasto->categoria_gasto_id = $request->categoria_gasto_id;
            $gasto->save();

            Session::flash('message', [ 'content' => "Gasto actualizado con éxito", 'type' => 'success' ]);
            return redirect()->route('gastos.index', [ 'mes' => substr($request->fecha, 0, 7) ]);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $gasto = Gasto::find($id);

            if (empty($gasto)) {
                Session::flash('message', [ 'content' => "El gasto con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('gastos.index');
            }

            $gasto->delete();

            Session::flash('message', [ 'content' => "Gasto eliminado con éxito", 'type' => 'success' ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    private function reglas(): array {
        return [
            'descripcion' => 'required|max:120',
            'monto' => 'required|numeric|min:0.01|max:9999999999',
            'fecha' => 'required|date',
            'categoria_gasto_id' => 'required|exists:categorias_gasto,id',
        ];
    }

    private function mensajes(): array {
        return [
            'descripcion.required' => 'La descripción es requerida',
            'descripcion.max' => 'La descripción no puede ser mayor a :max letras',

            'monto.required' => 'El monto es requerido',
            'monto.numeric' => 'El monto debe ser un número',
            'monto.min' => 'El monto debe ser mayor a cero',
            'monto.max' => 'El monto es demasiado grande',

            'fecha.required' => 'La fecha es requerida',
            'fecha.date' => 'La fecha no es válida',

            'categoria_gasto_id.required' => 'La categoría es requerida',
            'categoria_gasto_id.exists' => 'La categoría no existe',
        ];
    }
}
