@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Calculadora de propinas'])

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header"><h3>Datos de la cuenta</h3></div>

                    <div class="card-body mt-3">
                        <form action="{{ route('propinas.calcular') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="monto">Monto total de la cuenta</label>
                                <input type="number" step="0.01" min="0" name="monto" id="monto" class="form-control"
                                       value="{{ old('monto', $resultado['monto'] ?? '') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Porcentaje de propina</label>
                                <div class="btn-group d-flex mb-2" role="group">
                                    @foreach ([5, 10, 15, 20] as $p)
                                        <button type="button" class="btn btn-outline-primary"
                                                onclick="document.getElementById('porcentaje').value = {{ $p }}">{{ $p }}%</button>
                                    @endforeach
                                </div>
                                <input type="number" step="0.1" min="0" max="100" name="porcentaje" id="porcentaje"
                                       class="form-control" placeholder="Otro porcentaje"
                                       value="{{ old('porcentaje', $resultado['porcentaje'] ?? 10) }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="personas">¿Entre cuántas personas se divide? (opcional)</label>
                                <input type="number" min="1" max="100" name="personas" id="personas" class="form-control"
                                       value="{{ old('personas', $resultado['personas'] ?? 1) }}">
                            </div>

                            <button class="btn btn-primary"><i class="bi bi-calculator"></i> Calcular</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                @if ($resultado)
                    <div class="card">
                        <div class="card-header"><h3>Resultado</h3></div>

                        <div class="card-body mt-3">
                            <table class="table table-bordered mb-0">
                                <tr><th>Cuenta</th><td>$ {{ number_format($resultado['monto'], 2, ',', '.') }}</td></tr>
                                <tr><th>Propina ({{ rtrim(rtrim(number_format($resultado['porcentaje'], 2, ',', '.'), '0'), ',') }}%)</th>
                                    <td class="text-success fw-bold">$ {{ number_format($resultado['propina'], 2, ',', '.') }}</td></tr>
                                <tr><th>Total a pagar</th>
                                    <td class="fw-bold">$ {{ number_format($resultado['total'], 2, ',', '.') }}</td></tr>
                                @if ($resultado['personas'] > 1)
                                    <tr><th>Propina por persona ({{ $resultado['personas'] }})</th>
                                        <td>$ {{ number_format($resultado['propina_por_persona'], 2, ',', '.') }}</td></tr>
                                    <tr><th>Total por persona</th>
                                        <td>$ {{ number_format($resultado['total_por_persona'], 2, ',', '.') }}</td></tr>
                                @endif
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card"><div class="card-body mt-3 text-muted">
                        Ingresa el monto y elige el porcentaje para ver la propina.
                    </div></div>
                @endif
            </div>
        </div>
    </section>
@endsection
