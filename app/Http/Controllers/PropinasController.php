<?php

namespace App\Http\Controllers;

use App\Models\Propina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PropinasController extends Controller
{
    public function index() {
        return view('propinas/index', [ 'resultado' => null ]);
    }

    public function calcular(Request $request) {
        Validator::make($request->all(),
        [
            'monto' => 'required|numeric|min:0.01|max:999999999',
            'porcentaje' => 'required|numeric|min:0|max:100',
            'personas' => 'nullable|integer|min:1|max:100'
        ],
        [
            'monto.required' => 'El monto de la cuenta es requerido',
            'monto.numeric' => 'El monto debe ser un número',
            'monto.min' => 'El monto debe ser mayor a cero',
            'monto.max' => 'El monto es demasiado grande',

            'porcentaje.required' => 'El porcentaje de propina es requerido',
            'porcentaje.numeric' => 'El porcentaje debe ser un número',
            'porcentaje.min' => 'El porcentaje no puede ser negativo',
            'porcentaje.max' => 'El porcentaje no puede ser mayor a :max',

            'personas.integer' => 'Las personas deben ser un número entero',
            'personas.min' => 'Debe haber al menos :min persona',
            'personas.max' => 'No pueden ser más de :max personas',
        ])->validate();

        $resultado = Propina::calcular(
            (float) $request->monto,
            (float) $request->porcentaje,
            (int) ($request->personas ?: 1)
        );

        return view('propinas/index', [ 'resultado' => $resultado ]);
    }
}
