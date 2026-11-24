<?php

namespace App\Http\Controllers;

use App\Models\Encuesta;
use App\Models\Participacion;
use App\Models\Pregunta;
use App\Models\Respuesta;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class EncuestasController extends Controller
{
    public function index(Request $request) {
        $encuestas = Encuesta::withCount(['preguntas', 'participaciones'])
                             ->where('titulo', 'LIKE', "%$request->filter%")
                             ->orderByDesc('id')
                             ->paginate(10)->withQueryString();

        return view('encuestas/index', [ 'encuestas' => $encuestas, 'data' => $request ]);
    }

    public function create() {
        return view('encuestas/create');
    }

    public function store(Request $request) {
        $preguntas = collect($request->input('preguntas', []))
            ->map(function ($p) {
                $p['texto'] = isset($p['texto']) ? trim($p['texto']) : '';
                $p['opciones'] = array_values(array_filter(array_map('trim', $p['opciones'] ?? []), fn ($o) => $o !== ''));
                return $p;
            })
            ->filter(fn ($p) => $p['texto'] !== '' || count($p['opciones']) > 0)
            ->values()
            ->all();

        $request->merge([ 'preguntas' => $preguntas ]);

        Validator::make($request->all(),
        [
            'titulo' => 'required|max:120',
            'descripcion' => 'nullable|max:300',
            'preguntas' => 'required|array|min:1|max:20',
            'preguntas.*.texto' => 'required|max:200',
            'preguntas.*.opciones' => 'required|array|min:2|max:10',
            'preguntas.*.opciones.*' => 'required|max:100',
        ],
        [
            'titulo.required' => 'El título de la encuesta es requerido',
            'titulo.max' => 'El título no puede ser mayor a :max letras',

            'descripcion.max' => 'La descripción no puede ser mayor a :max letras',

            'preguntas.required' => 'La encuesta debe tener al menos una pregunta',
            'preguntas.min' => 'La encuesta debe tener al menos :min pregunta',
            'preguntas.max' => 'La encuesta no puede tener más de :max preguntas',

            'preguntas.*.texto.required' => 'Todas las preguntas deben tener texto',
            'preguntas.*.texto.max' => 'Una pregunta no puede ser mayor a :max letras',

            'preguntas.*.opciones.required' => 'Cada pregunta necesita opciones',
            'preguntas.*.opciones.min' => 'Cada pregunta debe tener al menos :min opciones',
            'preguntas.*.opciones.max' => 'Una pregunta no puede tener más de :max opciones',

            'preguntas.*.opciones.*.max' => 'Una opción no puede ser mayor a :max letras',
        ])->validate();

        try {
            DB::transaction(function () use ($request) {
                $encuesta = new Encuesta();
                $encuesta->titulo = trim($request->titulo);
                $encuesta->descripcion = $request->descripcion;
                $encuesta->save();

                foreach ($request->preguntas as $indice => $datos) {
                    $pregunta = new Pregunta();
                    $pregunta->encuesta_id = $encuesta->id;
                    $pregunta->texto = $datos['texto'];
                    $pregunta->orden = $indice + 1;
                    $pregunta->save();

                    foreach ($datos['opciones'] as $texto) {
                        $pregunta->opciones()->create([ 'texto' => $texto ]);
                    }
                }
            });

            Session::flash('message', [ 'content' => "Encuesta creada con éxito", 'type' => 'success' ]);
            return redirect()->route('encuestas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back()->withInput();
        }
    }

    public function toggle($id) {
        try {
            $encuesta = Encuesta::find($id);

            if (empty($encuesta)) {
                Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('encuestas.index');
            }

            $encuesta->abierta = !$encuesta->abierta;
            $encuesta->save();

            Session::flash('message', [
                'content' => $encuesta->abierta ? "Encuesta abierta a respuestas" : "Encuesta cerrada",
                'type' => 'success',
            ]);
            return redirect()->back();
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function delete($id) {
        try {
            $encuesta = Encuesta::find($id);

            if (empty($encuesta)) {
                Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
                return redirect()->route('encuestas.index');
            }

            $encuesta->delete();

            Session::flash('message', [ 'content' => "Encuesta eliminada con éxito", 'type' => 'success' ]);
            return redirect()->route('encuestas.index');
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function responder($id) {
        $encuesta = Encuesta::with('preguntas.opciones')->find($id);

        if (empty($encuesta)) {
            Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        if (!$encuesta->abierta) {
            Session::flash('message', [ 'content' => "Esta encuesta está cerrada y ya no recibe respuestas.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        return view('encuestas/responder', [ 'encuesta' => $encuesta ]);
    }

    public function guardarRespuestas(Request $request, $id) {
        $encuesta = Encuesta::with('preguntas.opciones')->find($id);

        if (empty($encuesta)) {
            Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        if (!$encuesta->abierta) {
            Session::flash('message', [ 'content' => "Esta encuesta está cerrada y ya no recibe respuestas.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        $errores = [];
        $elegidas = [];

        foreach ($encuesta->preguntas as $pregunta) {
            $opcionId = $request->input("respuestas.{$pregunta->id}");

            if (empty($opcionId) || !$pregunta->opciones->contains('id', (int) $opcionId)) {
                $errores[] = "Responde la pregunta: " . $pregunta->texto;
            } else {
                $elegidas[$pregunta->id] = (int) $opcionId;
            }
        }

        if (!empty($errores)) {
            return redirect()->back()->withErrors($errores)->withInput();
        }

        try {
            DB::transaction(function () use ($encuesta, $elegidas) {
                $participacion = new Participacion();
                $participacion->encuesta_id = $encuesta->id;
                $participacion->save();

                foreach ($elegidas as $preguntaId => $opcionId) {
                    $respuesta = new Respuesta();
                    $respuesta->participacion_id = $participacion->id;
                    $respuesta->pregunta_id = $preguntaId;
                    $respuesta->opcion_id = $opcionId;
                    $respuesta->save();
                }
            });

            Session::flash('message', [ 'content' => "¡Gracias por responder la encuesta!", 'type' => 'success' ]);
            return redirect()->route('encuestas.resultados', $encuesta->id);
        } catch(Exception $ex) {
            Log::error($ex);
            Session::flash('message', [ 'content' => "Ha ocurrido un error.", 'type' => 'error' ]);
            return redirect()->back();
        }
    }

    public function resultados($id) {
        $encuesta = Encuesta::find($id);

        if (empty($encuesta)) {
            Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        return view('encuestas/resultados', [ 'encuesta' => $encuesta, 'informe' => $encuesta->resultados() ]);
    }

    public function exportar($id) {
        $encuesta = Encuesta::find($id);

        if (empty($encuesta)) {
            Session::flash('message', [ 'content' => "La encuesta con id '$id' no existe.", 'type' => 'error' ]);
            return redirect()->route('encuestas.index');
        }

        $informe = $encuesta->resultados();

        return response()->streamDownload(function () use ($encuesta, $informe) {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, ['Encuesta', $encuesta->titulo]);
            fputcsv($salida, ['Participaciones', $informe['participaciones']]);
            fputcsv($salida, []);
            fputcsv($salida, ['Pregunta', 'Opción', 'Votos', 'Porcentaje']);

            foreach ($informe['preguntas'] as $pregunta) {
                foreach ($pregunta['opciones'] as $opcion) {
                    fputcsv($salida, [$pregunta['pregunta'], $opcion['texto'], $opcion['votos'], $opcion['porcentaje'] . '%']);
                }
            }

            fclose($salida);
        }, 'encuesta-' . $encuesta->id . '.csv', [ 'Content-Type' => 'text/csv; charset=UTF-8' ]);
    }
}
