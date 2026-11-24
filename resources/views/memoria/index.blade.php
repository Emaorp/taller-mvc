@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Juego de memoria'])

    <section class="section dashboard">
        <p class="text-muted">Encuentra todas las parejas de cartas con el menor número de intentos y el menor tiempo.
            Puntaje = 100 por pareja − 10 por cada intento extra − 2 por cada segundo.</p>

        <div class="row">
            @foreach ($niveles as $clave => $nivel)
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>{{ $nivel['nombre'] }} <small class="text-muted fs-6">({{ $nivel['pares'] }} parejas)</small></h3>
                        </div>

                        <div class="card-body mt-3">
                            <a href="{{ route('memoria.jugar', $clave) }}" class="btn btn-primary mb-3">
                                <i class="bi bi-play-fill"></i> Jugar
                            </a>

                            <h6>Mejores puntajes</h6>
                            <table class="table table-sm table-bordered mb-0">
                                <thead><tr><th>Jugador</th><th>Puntos</th><th>Mov.</th><th>Seg.</th></tr></thead>
                                <tbody>
                                    @forelse ($ranking[$clave] as $partida)
                                        <tr>
                                            <td>{{ $partida->jugador }}</td>
                                            <td><strong>{{ $partida->puntaje }}</strong></td>
                                            <td>{{ $partida->movimientos }}</td>
                                            <td>{{ $partida->segundos }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" class="text-center text-muted">Aún no hay partidas.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
