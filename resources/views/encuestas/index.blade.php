@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Plataforma de encuestas'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10"><h3>Encuestas</h3></div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('encuestas.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Crear</a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('encuestas.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-10">
                        <input type="text" name="filter" class="form-control" placeholder="Buscar..." value="{{ $data->filter }}">
                    </div>
                    <div class="col-md-auto"><button class="btn btn-primary"><i class="bi bi-search"></i></button></div>
                </form>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Encuesta</th>
                            <th>Preguntas</th>
                            <th>Respuestas</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($encuestas as $encuesta)
                            <tr>
                                <td>
                                    <strong>{{ $encuesta->titulo }}</strong>
                                    @if ($encuesta->descripcion)<br><small class="text-muted">{{ $encuesta->descripcion }}</small>@endif
                                </td>
                                <td>{{ $encuesta->preguntas_count }}</td>
                                <td>{{ $encuesta->participaciones_count }}</td>
                                <td>
                                    <span class="badge bg-{{ $encuesta->abierta ? 'success' : 'secondary' }}">
                                        {{ $encuesta->abierta ? 'Abierta' : 'Cerrada' }}
                                    </span>
                                </td>
                                <td class="text-nowrap">
                                    @if ($encuesta->abierta)
                                        <a href="{{ route('encuestas.responder', $encuesta->id) }}" class="btn btn-primary btn-sm" title="Responder">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('encuestas.resultados', $encuesta->id) }}" class="btn btn-info btn-sm" title="Resultados">
                                        <i class="bi bi-bar-chart-fill"></i>
                                    </a>

                                    <form action="{{ route('encuestas.toggle', $encuesta->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-warning btn-sm" title="{{ $encuesta->abierta ? 'Cerrar' : 'Abrir' }}">
                                            <i class="bi {{ $encuesta->abierta ? 'bi-lock-fill' : 'bi-unlock-fill' }}"></i>
                                        </button>
                                    </form>

                                    <form action="{{ route('encuestas.delete', $encuesta->id) }}" method="POST"
                                          class="d-inline form-eliminar" data-mensaje="¿Eliminar la encuesta y todas sus respuestas?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No hay encuestas.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $encuestas->links() }}
            </div>
        </div>
    </section>
@endsection
