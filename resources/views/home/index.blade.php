@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Taller de seguimiento'])

    <section class="section dashboard">
        <p class="text-muted">
            Once ejercicios independientes hechos con PHP / Laravel y arquitectura MVC:
            cada uno tiene su archivo de rutas, su controlador, su modelo (cuando guarda datos) y sus vistas.
        </p>

        <div class="row">
            @php
                $ejercicios = [
                    ['tareas.index', 'bi-check2-square', '1. Lista de tareas', 'Agrega, elimina y marca tareas como completadas.'],
                    ['propinas.index', 'bi-cash-coin', '2. Calculadora de propinas', 'Calcula la propina según el monto y el porcentaje.'],
                    ['contrasenas.index', 'bi-shield-lock', '3. Generador de contraseñas', 'Contraseñas seguras con longitud y tipos de caracteres.'],
                    ['gastos.index', 'bi-wallet2', '4. Gestor de gastos', 'Registra gastos por categoría y mira el resumen mensual.'],
                    ['reservas.index', 'bi-calendar-check', '5. Sistema de reservas', 'Reserva servicios viendo la disponibilidad y confirma.'],
                    ['notas.index', 'bi-journal-text', '6. Gestor de notas', 'Notas con categorías y búsqueda.'],
                    ['eventos.index', 'bi-calendar3', '7. Calendario de eventos', 'Calendario con eventos, recordatorios y notificaciones.'],
                    ['recetas.index', 'bi-egg-fried', '8. Plataforma de recetas', 'Busca, guarda y comparte recetas con filtros.'],
                    ['memoria.index', 'bi-grid-3x3-gap', '9. Juego de memoria', 'Empareja cartas, tres niveles y puntajes.'],
                    ['encuestas.index', 'bi-bar-chart-line', '10. Plataforma de encuestas', 'Crea y responde encuestas con informe visual.'],
                    ['cronometro.index', 'bi-stopwatch', '11. Cronómetro online', 'Inicio, pausa, reinicio y vueltas.'],
                ];
            @endphp

            @foreach ($ejercicios as [$ruta, $icono, $titulo, $texto])
                @continue(!Route::has($ruta))
                <div class="col-md-6 col-xl-4">
                    <div class="card info-card">
                        <div class="card-body">
                            <h5 class="card-title"><i class="bi {{ $icono }}"></i> {{ $titulo }}</h5>
                            <p>{{ $texto }}</p>
                            <a href="{{ route($ruta) }}" class="btn btn-primary btn-sm">Abrir</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
