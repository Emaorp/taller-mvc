@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Nivel ' . $nivel['nombre'], 'migas' => ['Juego de memoria' => route('memoria.index')]])

    <style>
        .tablero { display: grid; gap: 10px; grid-template-columns: repeat({{ $nivel['columnas'] }}, 1fr); max-width: {{ $nivel['columnas'] * 110 }}px; }
        .carta { aspect-ratio: 1; border: 0; border-radius: 12px; font-size: 2.2rem; background: #4154f1; color: #fff;
                 display: flex; align-items: center; justify-content: center; cursor: pointer; transition: transform .15s; }
        .carta:hover { transform: scale(1.04); }
        .carta.abierta, .carta.acertada { background: #fff; border: 2px solid #dee2e6; cursor: default; }
        .carta.acertada { border-color: #198754; opacity: .75; }
        .carta .cara { display: none; }
        .carta.abierta .cara, .carta.acertada .cara { display: inline; }
        .carta.abierta .dorso, .carta.acertada .dorso { display: none; }
    </style>

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <span class="me-4"><i class="bi bi-hand-index"></i> Movimientos: <strong id="movimientos">0</strong></span>
                        <span class="me-4"><i class="bi bi-clock"></i> Tiempo: <strong id="tiempo">0</strong> s</span>
                        <span><i class="bi bi-check2-all"></i> Parejas: <strong id="parejas">0</strong> / {{ $nivel['pares'] }}</span>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('memoria.jugar', $clave) }}" class="btn btn-outline-primary btn-sm">Reiniciar</a>
                        <a href="{{ route('memoria.index') }}" class="btn btn-secondary btn-sm">Salir</a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">
                <div class="tablero" id="tablero">
                    @foreach ($baraja as $carta)
                        <button type="button" class="carta" data-par="{{ $carta['par'] }}" aria-label="Carta">
                            <i class="bi bi-question-lg dorso"></i>
                            <i class="bi {{ $carta['icono'] }} cara" style="color: {{ $carta['color'] }}"></i>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <form id="form-guardar" action="{{ route('memoria.guardar') }}" method="POST" class="d-none">
            @csrf
            <input type="hidden" name="nivel" value="{{ $clave }}">
            <input type="hidden" name="jugador" id="f-jugador">
            <input type="hidden" name="movimientos" id="f-movimientos">
            <input type="hidden" name="segundos" id="f-segundos">
        </form>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const totalPares = {{ $nivel['pares'] }};
            const cartas = document.querySelectorAll('.carta');
            let abiertas = [], bloqueado = false, movimientos = 0, aciertos = 0, segundos = 0, reloj = null;

            function iniciarReloj() {
                if (reloj) return;
                reloj = setInterval(function () {
                    segundos++;
                    document.getElementById('tiempo').textContent = segundos;
                }, 1000);
            }

            function terminar() {
                clearInterval(reloj);

                Swal.fire({
                    title: '¡Ganaste!',
                    text: movimientos + ' movimientos en ' + segundos + ' segundos. Escribe tu nombre para guardar el puntaje:',
                    icon: 'success',
                    input: 'text',
                    inputAttributes: { maxlength: 40 },
                    inputValue: 'Jugador',
                    confirmButtonText: 'Guardar puntaje',
                    allowOutsideClick: false,
                    inputValidator: function (v) { return !v.trim() ? 'Escribe un nombre' : null; }
                }).then(function (r) {
                    document.getElementById('f-jugador').value = r.value.trim();
                    document.getElementById('f-movimientos').value = movimientos;
                    document.getElementById('f-segundos').value = Math.max(1, segundos);
                    document.getElementById('form-guardar').submit();
                });
            }

            cartas.forEach(function (carta) {
                carta.addEventListener('click', function () {
                    if (bloqueado || carta.classList.contains('abierta') || carta.classList.contains('acertada')) return;

                    iniciarReloj();
                    carta.classList.add('abierta');
                    abiertas.push(carta);

                    if (abiertas.length < 2) return;

                    movimientos++;
                    document.getElementById('movimientos').textContent = movimientos;
                    bloqueado = true;

                    const [a, b] = abiertas;

                    if (a.dataset.par === b.dataset.par) {
                        a.classList.replace('abierta', 'acertada');
                        b.classList.replace('abierta', 'acertada');
                        aciertos++;
                        document.getElementById('parejas').textContent = aciertos;
                        abiertas = [];
                        bloqueado = false;

                        if (aciertos === totalPares) setTimeout(terminar, 400);
                    } else {
                        setTimeout(function () {
                            a.classList.remove('abierta');
                            b.classList.remove('abierta');
                            abiertas = [];
                            bloqueado = false;
                        }, 800);
                    }
                });
            });
        })();
    </script>
@endpush
