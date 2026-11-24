@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Resumen mensual', 'migas' => ['Gastos' => route('gastos.index')]])

    <section class="section dashboard">

        <form action="{{ route('gastos.resumen') }}" method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <input type="month" name="mes" class="form-control" value="{{ $mes }}">
            </div>
            <div class="col-md-auto">
                <button class="btn btn-primary"><i class="bi bi-search"></i> Ver mes</button>
                <a href="{{ route('gastos.index', ['mes' => $mes]) }}" class="btn btn-secondary">Volver a la lista</a>
            </div>
        </form>

        <div class="row">
            <div class="col-md-4">
                <div class="card info-card"><div class="card-body">
                    <h5 class="card-title">Total de {{ $mes_texto }}</h5>
                    <h3>$ {{ number_format($resumen['total'], 2, ',', '.') }}</h3>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card info-card"><div class="card-body">
                    <h5 class="card-title">Gastos registrados</h5>
                    <h3>{{ $resumen['cantidad'] }}</h3>
                </div></div>
            </div>
            <div class="col-md-4">
                <div class="card info-card"><div class="card-body">
                    <h5 class="card-title">Promedio diario</h5>
                    <h3>$ {{ number_format($resumen['promedio_diario'], 2, ',', '.') }}</h3>
                </div></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3>Por categoría</h3></div>
                    <div class="card-body mt-3">
                        @forelse ($resumen['por_categoria'] as $fila)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $fila['categoria'] }} <small class="text-muted">({{ $fila['cantidad'] }})</small></span>
                                    <span>$ {{ number_format($fila['monto'], 2, ',', '.') }} · {{ $fila['porcentaje'] }}%</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" style="width: {{ $fila['porcentaje'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted mb-0">No hay gastos en este mes.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3>Últimos 6 meses</h3></div>
                    <div class="card-body mt-3">
                        @php $maximo = max(1, collect($historico)->max('total')); @endphp
                        @foreach ($historico as $fila)
                            <div class="mb-2">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('gastos.resumen', ['mes' => $fila['mes']]) }}">{{ $fila['etiqueta'] }}</a>
                                    <span>$ {{ number_format($fila['total'], 2, ',', '.') }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar bg-info" style="width: {{ $fila['total'] * 100 / $maximo }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
