@php
    $menu = [
        ['tareas.index',      'tareas*',      'bi-check2-square',   '1. Lista de tareas'],
        ['propinas.index',    'propinas*',    'bi-cash-coin',       '2. Calculadora de propinas'],
        ['contrasenas.index', 'contrasenas*', 'bi-shield-lock',     '3. Generador de contraseñas'],
        ['gastos.index',      'gastos*',      'bi-wallet2',         '4. Gestor de gastos'],
        ['reservas.index',    'reservas*',    'bi-calendar-check',  '5. Sistema de reservas'],
        ['notas.index',       'notas*',       'bi-journal-text',    '6. Gestor de notas'],
        ['eventos.index',     'eventos*',     'bi-calendar3',       '7. Calendario de eventos'],
        ['recetas.index',     'recetas*',     'bi-egg-fried',       '8. Plataforma de recetas'],
        ['memoria.index',     'memoria*',     'bi-grid-3x3-gap',    '9. Juego de memoria'],
        ['encuestas.index',   'encuestas*',   'bi-bar-chart-line',  '10. Plataforma de encuestas'],
        ['cronometro.index',  'cronometro*',  'bi-stopwatch',       '11. Cronómetro online'],
    ];
@endphp

<aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('home.index') ? '' : 'collapsed' }}" href="{{ route('home.index') }}">
                <i class="bi bi-grid"></i>
                <span>Inicio</span>
            </a>
        </li>

        @foreach ($menu as [$ruta, $patron, $icono, $texto])
            @continue(!Route::has($ruta))
            <li class="nav-item">
                <a class="nav-link {{ request()->is($patron) ? '' : 'collapsed' }}" href="{{ route($ruta) }}">
                    <i class="bi {{ $icono }}"></i>
                    <span>{{ $texto }}</span>
                </a>
            </li>
        @endforeach

    </ul>

</aside>
