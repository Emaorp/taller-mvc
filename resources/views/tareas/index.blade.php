@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Lista de tareas'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <h3>Lista de tareas
                    <small class="text-muted fs-6">({{ $completadas }} de {{ $total }} completadas)</small>
                </h3>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('tareas.store') }}" method="POST" class="row g-2 mb-4">
                    @csrf
                    <div class="col">
                        <input type="text" name="titulo" class="form-control" placeholder="Nueva tarea..."
                               maxlength="120" value="{{ old('titulo') }}" required>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Agregar</button>
                    </div>
                </form>

                <div class="btn-group mb-3" role="group">
                    <a href="{{ route('tareas.index') }}"
                       class="btn btn-sm {{ $filtro == 'todas' ? 'btn-primary' : 'btn-outline-primary' }}">Todas</a>
                    <a href="{{ route('tareas.index', ['estado' => 'pendientes']) }}"
                       class="btn btn-sm {{ $filtro == 'pendientes' ? 'btn-primary' : 'btn-outline-primary' }}">Pendientes</a>
                    <a href="{{ route('tareas.index', ['estado' => 'completadas']) }}"
                       class="btn btn-sm {{ $filtro == 'completadas' ? 'btn-primary' : 'btn-outline-primary' }}">Completadas</a>
                </div>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th style="width: 60px">Hecha</th>
                            <th>Tarea</th>
                            <th style="width: 110px"></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($tareas as $tarea)
                            <tr>
                                <td class="text-center">
                                    <form action="{{ route('tareas.toggle', $tarea->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm {{ $tarea->completada ? 'btn-success' : 'btn-outline-secondary' }}"
                                                title="Marcar como {{ $tarea->completada ? 'pendiente' : 'completada' }}">
                                            <i class="bi {{ $tarea->completada ? 'bi-check-square-fill' : 'bi-square' }}"></i>
                                        </button>
                                    </form>
                                </td>
                                <td class="{{ $tarea->completada ? 'text-decoration-line-through text-muted' : '' }}">
                                    {{ $tarea->titulo }}
                                </td>
                                <td>
                                    <form action="{{ route('tareas.delete', $tarea->id) }}" method="POST"
                                          class="form-eliminar" data-mensaje="¿Eliminar la tarea?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay tareas para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
