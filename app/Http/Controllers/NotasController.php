<?php

namespace App\Http\Controllers;

use App\Models\CategoriaNota;
use App\Models\Nota;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class NotasController extends Controller
{
    public function index(Request $request) {
        $consulta = Nota::with('categoria')->buscar($request->filter);

        if (!empty($request->categoria_id)) {
            $consulta->where('categoria_nota_id', $request->categoria_id);
        }

        return view('notas/index', [
            'notas' => $consulta->orderByDesc('updated_at')->paginate(12)->withQueryString(),
            'categorias' => CategoriaNota::orderBy('nombre')->get(),
            'data' => $request,
        ]);
    }

    public function create() {
        return view('notas/create', [ 'categorias' => CategoriaNota::orderBy('nombre')->get() ]);
    }

    public function edit($id) {
        $nota = Nota::find($id);

        if (empty($nota)) {
            Session::flash('message', [ 'content' => "La nota con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('notas.index');
        }

        return view('notas/edit', [
            'nota' => $nota,
            'categorias' => CategoriaNota::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request) {
        Validator::make($request->all(), $this->reglas(), $this->mensajes())->validate();

        try {
            $nota = new Nota();
            $nota->titulo = trim($request->titulo);
            $nota->contenido = $request->contenido;
            $nota->categoria_nota_id = $request->categoria_nota_id;
            $nota->save();

            Session::flash('message', [ 'content' => "Nota guardada con éxito", 'type' => 'success' ]);
            return redirect()->route('notas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function update(Request $request) {
        Validator::make($request->all(),
            $this->reglas() + [ 'nota_id' => 'required|exists:notas,id' ],
            $this->mensajes() + [
                'nota_id.required' => 'La nota es requerida',
                'nota_id.exists' => 'El id dado no existe',
            ]
        )->validate();

        try {
            $nota = Nota::find($request->nota_id);
            $nota->titulo = trim($request->titulo);
            $nota->contenido = $request->contenido;
            $nota->categoria_nota_id = $request->categoria_nota_id;
            $nota->save();

            Session::flash('message', [ 'content' => "Nota actualizada con éxito", 'type' => 'success' ]);
            return redirect()->route('notas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $nota = Nota::find($id);

            if (empty($nota)) {
                Session::flash('message', [ 'content' => "La nota con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('notas.index');
            }

            $nota->delete();

            Session::flash('message', [ 'content' => "Nota eliminada con éxito", 'type' => 'success' ]);
            return redirect()->route('notas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function categorias() {
        return view('notas/categorias', [
            'categorias' => CategoriaNota::withCount('notas')->orderBy('nombre')->get(),
        ]);
    }

    public function storeCategoria(Request $request) {
        Validator::make($request->all(),
        [
            'nombre' => 'required|max:40|unique:categorias_nota,nombre'
        ],
        [
            'nombre.required' => 'El nombre de la categoría es requerido',
            'nombre.max' => 'El nombre no puede ser mayor a :max letras',
            'nombre.unique' => 'Ya existe una categoría con ese nombre',
        ])->validate();

        try {
            $categoria = new CategoriaNota();
            $categoria->nombre = trim($request->nombre);
            $categoria->save();

            Session::flash('message', [ 'content' => "Categoría creada con éxito", 'type' => 'success' ]);
            return redirect()->route('notas.categorias');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function deleteCategoria($id) {
        try {
            $categoria = CategoriaNota::withCount('notas')->find($id);

            if (empty($categoria)) {
                Session::flash('message', [ 'content' => "La categoría con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('notas.categorias');
            }

            if ($categoria->notas_count > 0) {
                Session::flash('message', [ 'content' => "No se puede eliminar una categoría que tiene notas.", 'type' => 'error' ]);
                return redirect()->route('notas.categorias');
            }

            $categoria->delete();

            Session::flash('message', [ 'content' => "Categoría eliminada con éxito", 'type' => 'success' ]);
            return redirect()->route('notas.categorias');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    private function reglas(): array {
        return [
            'titulo' => 'required|max:120',
            'contenido' => 'required|max:5000',
            'categoria_nota_id' => 'required|exists:categorias_nota,id',
        ];
    }

    private function mensajes(): array {
        return [
            'titulo.required' => 'El título es requerido',
            'titulo.max' => 'El título no puede ser mayor a :max letras',

            'contenido.required' => 'El contenido es requerido',
            'contenido.max' => 'El contenido no puede ser mayor a :max letras',

            'categoria_nota_id.required' => 'La categoría es requerida',
            'categoria_nota_id.exists' => 'La categoría no existe',
        ];
    }
}
