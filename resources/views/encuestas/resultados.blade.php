@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Resultados', 'migas' => ['Encuestas' => route('encuestas.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8">
                        <h3>{{ $encuesta->titulo }}</h3>
                        <p class="mb-0 text-muted">
                            {{ $informe['participaciones'] }} {{ $informe['participaciones'] == 1 ? 'participación' : 'participaciones' }}
                            · {{ $encuesta->abierta ? 'Abierta' : 'Cerrada' }}
                        </p>
                    </div>
                    <div class="col-md-4 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                            <i class="bi bi-printer-fill"></i> Imprimir informe
                        </button>
                        <a href="{{ route('encuestas.exportar', $encuesta->id) }}" class="btn btn-success btn-sm">
                            <i class="bi bi-file-earmark-spreadsheet-fill"></i> Exportar CSV
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($informe['preguntas'] as $i => $pregunta)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body mt-3">
                            <h5>{{ $loop->iteration }}. {{ $pregunta['pregunta'] }}
                                <small class="text-muted">({{ $pregunta['total'] }} votos)</small>
                            </h5>

                            <canvas id="grafica-{{ $i }}" height="160"></canvas>

                            <table class="table table-sm mt-3 mb-0">
                                <thead><tr><th>Opción</th><th class="text-end">Votos</th><th class="text-end">%</th></tr></thead>
                                <tbody>
                                    @foreach ($pregunta['opciones'] as $opcion)
                                        <tr>
                                            <td>{{ $opcion['texto'] }}</td>
                                            <td class="text-end">{{ $opcion['votos'] }}</td>
                                            <td class="text-end">{{ $opcion['porcentaje'] }}%</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
    <script>
        const informe = @json($informe['preguntas']);
        const colores = ['#4154f1', '#2eca6a', '#ff771d', '#ff4d6d', '#6f42c1', '#17a2b8', '#ffc107', '#6c757d', '#20c997', '#fd7e14'];

        informe.forEach(function (pregunta, i) {
            new Chart(document.getElementById('grafica-' + i), {
                type: 'bar',
                data: {
                    labels: pregunta.opciones.map(function (o) { return o.texto; }),
                    datasets: [{
                        label: 'Votos',
                        data: pregunta.opciones.map(function (o) { return o.votos; }),
                        backgroundColor: pregunta.opciones.map(function (o, k) { return colores[k % colores.length]; })
                    }]
                },
                options: {
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
                }
            });
        });
    </script>
@endpush
