@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Sistema de reservas'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10"><h3>Reservas</h3></div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('reservas.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle-fill"></i> Reservar
                        </a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('reservas.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-5">
                        <input type="text" name="filter" class="form-control" placeholder="Buscar por nombre o correo..."
                               value="{{ $data->filter }}">
                    </div>
                    <div class="col-md-3">
                        <select name="estado" class="form-select">
                            <option value="">Todos los estados</option>
                            @foreach (['Pendiente', 'Confirmada', 'Cancelada'] as $estado)
                                <option value="{{ $estado }}" {{ $data->estado == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Hora</th>
                            <th>Servicio</th>
                            <th>Cliente</th>
                            <th>Estado</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($reservas as $reserva)
                            @php
                                $color = ['Pendiente' => 'warning', 'Confirmada' => 'success', 'Cancelada' => 'secondary'][$reserva->estado];
                            @endphp
                            <tr>
                                <td>{{ $reserva->fecha->format('d/m/Y') }}</td>
                                <td>{{ $reserva->hora }}</td>
                                <td>{{ $reserva->servicio->nombre }}</td>
                                <td>{{ $reserva->nombre }}<br><small class="text-muted">{{ $reserva->email }}</small></td>
                                <td><span class="badge bg-{{ $color }}">{{ $reserva->estado }}</span></td>
                                <td class="text-nowrap">
                                    @if ($reserva->estado == 'Pendiente')
                                        <form action="{{ route('reservas.confirm', $reserva->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-success btn-sm" title="Confirmar"><i class="bi bi-check-lg"></i></button>
                                        </form>
                                    @endif

                                    @if ($reserva->estado != 'Cancelada')
                                        <form action="{{ route('reservas.cancel', $reserva->id) }}" method="POST"
                                              class="d-inline form-eliminar" data-mensaje="¿Cancelar la reserva?">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-warning btn-sm" title="Cancelar"><i class="bi bi-x-lg"></i></button>
                                        </form>
                                    @endif

                                    <form action="{{ route('reservas.delete', $reserva->id) }}" method="POST"
                                          class="d-inline form-eliminar" data-mensaje="¿Eliminar la reserva?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No hay reservas.</td></tr>
                        @endforelse
                    </tbody>
                </table>

                {{ $reservas->links() }}
            </div>
        </div>
    </section>
@endsection
