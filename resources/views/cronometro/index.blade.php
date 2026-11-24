@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Cronómetro online'])

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-body mt-3 text-center">
                        <div id="pantalla" class="display-2 font-monospace mb-4">00:00.00</div>

                        <div class="mb-4">
                            <button type="button" id="btn-iniciar" class="btn btn-success btn-lg"><i class="bi bi-play-fill"></i> Iniciar</button>
                            <button type="button" id="btn-vuelta" class="btn btn-primary btn-lg" disabled><i class="bi bi-flag-fill"></i> Vuelta</button>
                            <button type="button" id="btn-reiniciar" class="btn btn-secondary btn-lg" disabled><i class="bi bi-arrow-counterclockwise"></i> Reiniciar</button>
                        </div>

                        <table class="table table-bordered table-sm text-center">
                            <thead><tr><th>Vuelta</th><th>Parcial</th><th>Total</th></tr></thead>
                            <tbody id="tabla-vueltas">
                                <tr id="sin-vueltas"><td colspan="3" class="text-muted">Aún no hay vueltas.</td></tr>
                            </tbody>
                        </table>

                        <form action="{{ route('cronometro.store') }}" method="POST" class="row g-2 mt-3" id="form-guardar">
                            @csrf
                            <input type="hidden" name="duracion_ms" id="f-duracion">
                            <input type="hidden" name="vueltas" id="f-vueltas">
                            <div class="col">
                                <input type="text" name="nombre" class="form-control" maxlength="60" placeholder="Nombre de la sesión" required>
                            </div>
                            <div class="col-auto">
                                <button class="btn btn-outline-primary"><i class="bi bi-save-fill"></i> Guardar sesión</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3>Sesiones guardadas</h3></div>
                    <div class="card-body mt-3">
                        @forelse ($sesiones as $sesion)
                            <div class="border rounded p-2 mb-2">
                                <div class="d-flex justify-content-between">
                                    <strong>{{ $sesion->nombre }}</strong>
                                    <span class="font-monospace">{{ \App\Models\SesionCronometro::formatear($sesion->duracion_ms) }}</span>
                                </div>
                                <small class="text-muted">
                                    {{ $sesion->created_at->format('d/m/Y H:i') }} · {{ count($sesion->vueltas ?? []) }} vueltas
                                </small>

                                @if (!empty($sesion->vueltas))
                                    <div class="small mt-1">
                                        @foreach ($sesion->vueltas as $i => $total)
                                            <span class="badge bg-light text-dark">V{{ $i + 1 }}: {{ \App\Models\SesionCronometro::formatear($total) }}</span>
                                        @endforeach
                                    </div>
                                @endif

                                <form action="{{ route('cronometro.delete', $sesion->id) }}" method="POST"
                                      class="form-eliminar mt-2" data-mensaje="¿Eliminar la sesión?">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                </form>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Todavía no has guardado sesiones.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const pantalla = document.getElementById('pantalla');
            const btnIniciar = document.getElementById('btn-iniciar');
            const btnVuelta = document.getElementById('btn-vuelta');
            const btnReiniciar = document.getElementById('btn-reiniciar');
            const tabla = document.getElementById('tabla-vueltas');

            let acumulado = 0;
            let inicio = 0;
            let intervalo = null;
            let vueltas = [];

            function formatear(ms) {
                const cs = Math.floor((ms % 1000) / 10);
                const s = Math.floor(ms / 1000) % 60;
                const m = Math.floor(ms / 60000) % 60;
                const h = Math.floor(ms / 3600000);
                const base = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0') + '.' + String(cs).padStart(2, '0');
                return h > 0 ? String(h).padStart(2, '0') + ':' + base : base;
            }

            function transcurrido() {
                return acumulado + (intervalo ? performance.now() - inicio : 0);
            }

            function pintar() {
                pantalla.textContent = formatear(transcurrido());
            }

            btnIniciar.addEventListener('click', function () {
                if (intervalo) {
                    acumulado = transcurrido();
                    clearInterval(intervalo);
                    intervalo = null;
                    btnIniciar.innerHTML = '<i class="bi bi-play-fill"></i> Reanudar';
                    btnIniciar.className = 'btn btn-success btn-lg';
                    btnVuelta.disabled = true;
                } else {
                    inicio = performance.now();
                    intervalo = setInterval(pintar, 30);
                    btnIniciar.innerHTML = '<i class="bi bi-pause-fill"></i> Pausar';
                    btnIniciar.className = 'btn btn-warning btn-lg';
                    btnVuelta.disabled = false;
                    btnReiniciar.disabled = false;
                }
                pintar();
            });

            btnVuelta.addEventListener('click', function () {
                const total = Math.round(transcurrido());
                const anterior = vueltas.length ? vueltas[vueltas.length - 1] : 0;
                vueltas.push(total);

                const vacio = document.getElementById('sin-vueltas');
                if (vacio) vacio.remove();

                const fila = document.createElement('tr');
                fila.innerHTML = '<td>' + vueltas.length + '</td><td>' + formatear(total - anterior) + '</td><td>' + formatear(total) + '</td>';
                tabla.insertBefore(fila, tabla.firstChild);
            });

            btnReiniciar.addEventListener('click', function () {
                clearInterval(intervalo);
                intervalo = null;
                acumulado = 0;
                vueltas = [];
                tabla.innerHTML = '<tr id="sin-vueltas"><td colspan="3" class="text-muted">Aún no hay vueltas.</td></tr>';
                btnIniciar.innerHTML = '<i class="bi bi-play-fill"></i> Iniciar';
                btnIniciar.className = 'btn btn-success btn-lg';
                btnVuelta.disabled = true;
                btnReiniciar.disabled = true;
                pintar();
            });

            document.getElementById('form-guardar').addEventListener('submit', function () {
                document.getElementById('f-duracion').value = Math.round(transcurrido());
                document.getElementById('f-vueltas').value = JSON.stringify(vueltas);
            });
        })();
    </script>
@endpush
