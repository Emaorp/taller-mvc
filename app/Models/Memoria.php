<?php

namespace App\Models;

class Memoria
{
    const NIVELES = [
        'facil' => ['nombre' => 'Fácil', 'pares' => 6, 'columnas' => 4],
        'medio' => ['nombre' => 'Medio', 'pares' => 8, 'columnas' => 4],
        'dificil' => ['nombre' => 'Difícil', 'pares' => 12, 'columnas' => 6],
    ];

    const IMAGENES = [
        ['bi-heart-fill', '#e63946'],
        ['bi-star-fill', '#f4a100'],
        ['bi-moon-stars-fill', '#6a4c93'],
        ['bi-sun-fill', '#ff9f1c'],
        ['bi-lightning-charge-fill', '#d4a017'],
        ['bi-cloud-fill', '#4ea8de'],
        ['bi-flower1', '#e07a9f'],
        ['bi-gem', '#2a9d8f'],
        ['bi-bell-fill', '#b5838d'],
        ['bi-bug-fill', '#588157'],
        ['bi-emoji-smile-fill', '#f77f00'],
        ['bi-rocket-takeoff-fill', '#3a86ff'],
    ];

    public static function nivelValido(?string $nivel): bool {
        return $nivel !== null && isset(self::NIVELES[$nivel]);
    }

    public static function generarBaraja(string $nivel): array {
        $pares = self::NIVELES[$nivel]['pares'];

        $cartas = [];
        foreach (array_slice(self::IMAGENES, 0, $pares) as $indice => [$icono, $color]) {
            $cartas[] = ['par' => $indice, 'icono' => $icono, 'color' => $color];
            $cartas[] = ['par' => $indice, 'icono' => $icono, 'color' => $color];
        }

        for ($i = count($cartas) - 1; $i > 0; $i--) {
            $j = random_int(0, $i);
            [$cartas[$i], $cartas[$j]] = [$cartas[$j], $cartas[$i]];
        }

        return $cartas;
    }

    public static function calcularPuntaje(string $nivel, int $movimientos, int $segundos): int {
        $pares = self::NIVELES[$nivel]['pares'];

        return max(0, $pares * 100 - ($movimientos - $pares) * 10 - $segundos * 2);
    }
}
