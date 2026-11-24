@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Gestor de gastos'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-9"><h3>Gastos del mes</h3></div>
                    <div class="col-md-3 text-end">
                        <a href="{{ route('gastos.resumen', ['mes' => $mes]) }}" class="btn btn-info">
                            <i class="bi bi-pie-chart-fill"></i> Resumen
                        </a>
                        <a href="{{ route('gastos.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle-fill"></i> Nuevo
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('gastos.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="month" name="mes" class="form-control" value="{{ $mes }}">
                    </div>
                    <div class="col-md-5">
                        <select name="categoria_id" class="form-select">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary"><i class="bi bi-search"></i> Filtrar</button>
                    </div>
                    <div class="col text-end align-self-center">
                        Total del mes: <strong>$ {{ number_format($total, 2, ',', '.') }}</strong>
                    </div>
                </form>

                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Descripción</th>
                            <th>Categoría</th>
                            <th class="text-end">Monto</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($gastos as $gasto)
                            <tr>
                                <td>{{ $gasto->fecha->format('d/m/Y') }}</td>
                                <td>{{ $gasto->descripcion }}</td>
                                <td><span class="badge bg-secondary">{{ $gasto->categoria->nombre }}</span></td>
                                <td class="text-end">$ {{ number_format($gasto->monto, 2, ',', '.') }}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('gastos.edit', $gasto->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('gastos.delete', $gasto->id) }}" method="POST"
                                          class="d-inline form-eliminar" data-mensaje="¿Eliminar este gasto?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No hay gastos en este mes.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $gastos->links() }}
            </div>
        </div>
    </section>
@endsection
