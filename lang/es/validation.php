<?php

return [
    'required' => 'El campo :attribute es requerido.',
    'string' => 'El campo :attribute debe ser texto.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'numeric' => 'El campo :attribute debe ser un número.',
    'email' => 'El campo :attribute debe ser un correo válido.',
    'date' => 'El campo :attribute no es una fecha válida.',
    'date_format' => 'El campo :attribute no tiene el formato :format.',
    'after_or_equal' => 'El campo :attribute debe ser una fecha igual o posterior a :date.',
    'exists' => 'El :attribute seleccionado no es válido.',
    'in' => 'El :attribute seleccionado no es válido.',
    'unique' => 'Ya existe un registro con ese :attribute.',
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'array' => 'El campo :attribute debe ser una lista.',
    'min' => [
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'array' => 'El campo :attribute debe tener al menos :min elementos.',
    ],
    'max' => [
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
        'numeric' => 'El campo :attribute no puede ser mayor a :max.',
        'array' => 'El campo :attribute no puede tener más de :max elementos.',
    ],
    'attributes' => [],
];
