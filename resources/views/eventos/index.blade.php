@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Calendario de eventos'])

    <section class="section dashboard">
        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-7">
                                <a href="{{ route('eventos.index', ['mes' => $anterior]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                                <span class="mx-2 fs-4 text-capitalize">{{ $mes_texto }}</span>
                                <a href="{{ route('eventos.index', ['mes' => $siguiente]) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                                <a href="{{ route('eventos.index') }}" class="btn btn-outline-secondary btn-sm ms-2">Hoy</a>
                            </div>
                            <div class="col-md-5 text-end">
                                <button type="button" id="btn-notificaciones" class="btn btn-info btn-sm">
                                    <i class="bi bi-bell-fill"></i> Activar notificaciones
                                </button>
                                <a href="{{ route('eventos.create') }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-plus-circle-fill"></i> Nuevo evento
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card-body mt-3 table-responsive">
                        <table class="table table-bordered" style="table-layout: fixed; min-width: 640px;">
                            <thead>
                                <tr class="text-center">
                                    @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $d)
                                        <th>{{ $d }}</th>
                                    @endforeach
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($semanas as $semana)
                                    <tr>
                                        @foreach ($semana as $dia)
                                            <td style="height: 105px; vertical-align: top;"
                                                class="{{ $dia['en_mes'] ? '' : 'bg-light text-muted' }} {{ $dia['es_hoy'] ? 'border-primary border-2' : '' }}">
                                                <a href="{{ route('eventos.create', ['fecha' => $dia['fecha']->toDateString()]) }}"
                                                   class="fw-bold text-decoration-none {{ $dia['es_hoy'] ? 'badge bg-primary' : '' }}"
                                                   title="Agregar evento este día">{{ $dia['fecha']->day }}</a>

                                                @foreach ($dia['eventos'] as $evento)
                                                    <a href="{{ route('eventos.edit', $evento->id) }}"
                                                       class="d-block small text-truncate rounded bg-primary bg-opacity-10 text-primary px-1 mt-1"
                                                       title="{{ $evento->titulo }}">
                                                        @if ($evento->recordatorio_min) <i class="bi bi-bell-fill"></i> @endif
                                                        {{ $evento->hora }} {{ $evento->titulo }}
                                                    </a>
                                                @endforeach
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-xl-3">
                <div class="card">
                    <div class="card-header"><h3>Próximos</h3></div>
                    <div class="card-body mt-3">
                        @forelse ($proximos as $evento)
                            <div class="mb-3">
                                <a href="{{ route('eventos.edit', $evento->id) }}" class="fw-bold">{{ $evento->titulo }}</a><br>
                                <small class="text-muted">
                                    {{ $evento->fecha->format('d/m/Y') }} {{ $evento->hora }}
                                    @if ($evento->recordatorio_min)
                                        · <i class="bi bi-bell-fill"></i> {{ \App\Models\Evento::RECORDATORIOS[$evento->recordatorio_min] ?? '' }}
                                    @endif
                                </small>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No hay eventos próximos.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.getElementById('btn-notificaciones').addEventListener('click', function () {
            if (!window.Notification) {
                Swal.fire({ title: 'Tu navegador no soporta notificaciones', icon: 'info' });
                return;
            }

            Notification.requestPermission().then(function (permiso) {
                Swal.fire({
                    title: permiso === 'granted' ? 'Notificaciones activadas' : 'Notificaciones no permitidas',
                    text: permiso === 'granted' ? 'Te avisaremos de los recordatorios de tus eventos.' : 'Igual verás los avisos dentro de la página.',
                    icon: permiso === 'granted' ? 'success' : 'info'
                });
            });
        });
    </script>
@endpush
