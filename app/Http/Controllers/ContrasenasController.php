<?php

namespace App\Http\Controllers;

use App\Models\GeneradorContrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContrasenasController extends Controller
{
    public function index() {
        return view('contrasenas/index', [
            'contrasenas' => [],
            'opciones' => $this->opcionesPorDefecto(),
            'fortaleza' => null,
        ]);
    }

    public function generar(Request $request) {
        $validador = Validator::make($request->all(),
        [
            'longitud' => 'required|integer|min:4|max:64',
            'cantidad' => 'required|integer|min:1|max:10',
        ],
        [
            'longitud.required' => 'La longitud es requerida',
            'longitud.integer' => 'La longitud debe ser un número entero',
            'longitud.min' => 'La longitud mínima es :min caracteres',
            'longitud.max' => 'La longitud máxima es :max caracteres',

            'cantidad.required' => 'La cantidad es requerida',
            'cantidad.integer' => 'La cantidad debe ser un número entero',
            'cantidad.min' => 'Debes generar al menos :min contraseña',
            'cantidad.max' => 'No puedes generar más de :max contraseñas',
        ]);

        $opciones = [
            'longitud' => (int) $request->longitud,
            'cantidad' => (int) $request->cantidad,
            'minusculas' => $request->boolean('minusculas'),
            'mayusculas' => $request->boolean('mayusculas'),
            'numeros' => $request->boolean('numeros'),
            'simbolos' => $request->boolean('simbolos'),
            'evitar_ambiguos' => $request->boolean('evitar_ambiguos'),
        ];

        $validador->after(function ($v) use ($opciones) {
            if (!$opciones['minusculas'] && !$opciones['mayusculas'] && !$opciones['numeros'] && !$opciones['simbolos']) {
                $v->errors()->add('tipos', 'Selecciona al menos un tipo de carácter');
            }
        });

        $validador->validate();

        $conjuntos = GeneradorContrasena::conjuntos(
            $opciones['minusculas'], $opciones['mayusculas'], $opciones['numeros'],
            $opciones['simbolos'], $opciones['evitar_ambiguos']
        );

        $contrasenas = [];

        for ($i = 0; $i < $opciones['cantidad']; $i++) {
            $contrasenas[] = GeneradorContrasena::generar($opciones['longitud'], $conjuntos);
        }

        return view('contrasenas/index', [
            'contrasenas' => $contrasenas,
            'opciones' => $opciones,
            'fortaleza' => GeneradorContrasena::fortaleza($opciones['longitud'], $conjuntos),
        ]);
    }

    private function opcionesPorDefecto(): array {
        return [
            'longitud' => 16, 'cantidad' => 1,
            'minusculas' => true, 'mayusculas' => true, 'numeros' => true, 'simbolos' => true,
            'evitar_ambiguos' => false,
        ];
    }
}
