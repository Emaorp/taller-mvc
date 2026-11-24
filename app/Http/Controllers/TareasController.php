<?php

namespace App\Http\Controllers;

use App\Models\Tarea;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class TareasController extends Controller
{
    public function index(Request $request) {
        $filtro = in_array($request->estado, ['pendientes', 'completadas']) ? $request->estado : 'todas';

        $consulta = Tarea::query();

        if ($filtro == 'pendientes') {
            $consulta->where('completada', false);
        } elseif ($filtro == 'completadas') {
            $consulta->where('completada', true);
        }

        $tareas = $consulta->orderBy('completada')->orderByDesc('id')->get();

        return view('tareas/index', [
            'tareas' => $tareas,
            'filtro' => $filtro,
            'total' => Tarea::count(),
            'completadas' => Tarea::where('completada', true)->count(),
        ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(),
        [
            'titulo' => 'required|max:120'
        ],
        [
            'titulo.required' => 'El título de la tarea es requerido',
            'titulo.max' => 'El título no puede ser mayor a :max letras'
        ])->validate();

        try {
            $tarea = new Tarea();
            $tarea->titulo = trim($request->titulo);
            $tarea->save();

            Session::flash('message', [ 'content' => "Tarea agregada con éxito", 'type' => 'success' ]);
            return redirect()->route('tareas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function toggle($id) {
        try {
            $tarea = Tarea::find($id);

            if (empty($tarea)) {
                Session::flash('message', [ 'content' => "La tarea con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('tareas.index');
            }

            $tarea->completada = !$tarea->completada;
            $tarea->save();

            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $tarea = Tarea::find($id);

            if (empty($tarea)) {
                Session::flash('message', [ 'content' => "La tarea con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('tareas.index');
            }

            $tarea->delete();

            Session::flash('message', [ 'content' => "Tarea eliminada con éxito", 'type' => 'success' ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }
}
