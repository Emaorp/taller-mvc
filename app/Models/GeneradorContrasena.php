<?php

namespace App\Models;

class GeneradorContrasena
{
    const MINUSCULAS = 'abcdefghijklmnopqrstuvwxyz';
    const MAYUSCULAS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const NUMEROS = '0123456789';
    const SIMBOLOS = '!@#$%^&*()-_=+[]{};:,.<>?';
    const AMBIGUOS = 'il1Lo0O';

    public static function conjuntos(bool $minusculas, bool $mayusculas, bool $numeros, bool $simbolos, bool $evitarAmbiguos): array {
        $conjuntos = [];

        if ($minusculas) $conjuntos[] = self::MINUSCULAS;
        if ($mayusculas) $conjuntos[] = self::MAYUSCULAS;
        if ($numeros) $conjuntos[] = self::NUMEROS;
        if ($simbolos) $conjuntos[] = self::SIMBOLOS;

        if ($evitarAmbiguos) {
            $conjuntos = array_map(fn ($c) => str_replace(str_split(self::AMBIGUOS), '', $c), $conjuntos);
        }

        return $conjuntos;
    }

    public static function generar(int $longitud, array $conjuntos): string {
        $caracteres = [];

        foreach ($conjuntos as $conjunto) {
            $caracteres[] = $conjunto[random_int(0, strlen($conjunto) - 1)];
        }

        $todos = implode('', $conjuntos);

        while (count($caracteres) < $longitud) {
            $caracteres[] = $todos[random_int(0, strlen($todos) - 1)];
        }

        for ($i = count($caracteres) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$caracteres[$i], $caracteres[$j]] = [$caracteres[$j], $caracteres[$i]];
        }

        return implode('', $caracteres);
    }

    public static function fortaleza(int $longitud, array $conjuntos): array {
        $bits = (int) round($longitud * log(max(2, strlen(implode('', $conjuntos))), 2));

        if ($bits < 40) return [$bits, 'Débil', 'danger'];
        if ($bits < 60) return [$bits, 'Media', 'warning'];
        if ($bits < 80) return [$bits, 'Fuerte', 'info'];

        return [$bits, 'Muy fuerte', 'success'];
    }
}
