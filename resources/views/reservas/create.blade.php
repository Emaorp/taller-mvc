@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Nueva reserva', 'migas' => ['Reservas' => route('reservas.index')]])

    <section class="section dashboard">
        <div class="row">

            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3>1. Servicio y fecha</h3></div>

                    <div class="card-body mt-3">
                        <form action="{{ route('reservas.create') }}" method="GET">
                            <div class="mb-3">
                                <label class="form-label" for="servicio_id">Servicio</label>
                                <select name="servicio_id" id="servicio_id" class="form-select">
                                    @foreach ($servicios as $servicio)
                                        <option value="{{ $servicio->id }}" {{ $servicio_id == $servicio->id ? 'selected' : '' }}>
                                            {{ $servicio->nombre }} ({{ $servicio->duracion_min }} min)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="fecha">Fecha</label>
                                <input type="date" name="fecha" id="fecha" class="form-control"
                                       min="{{ now()->toDateString() }}" value="{{ $fecha }}">
                            </div>

                            <button class="btn btn-info"><i class="bi bi-search"></i> Ver disponibilidad</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3>2. Elige la hora y confirma tus datos</h3></div>

                    <div class="card-body mt-3">
                        <form action="{{ route('reservas.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="servicio_id" value="{{ $servicio_id }}">
                            <input type="hidden" name="fecha" value="{{ $fecha }}">

                            <p class="mb-2">Disponibilidad del <strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong>:</p>

                            <div class="mb-3">
                                @foreach ($disponibilidad as $hora => $libre)
                                    <input type="radio" class="btn-check" name="hora" id="hora-{{ $loop->index }}"
                                           value="{{ $hora }}" {{ $libre ? '' : 'disabled' }} {{ old('hora') == $hora ? 'checked' : '' }}>
                                    <label class="btn {{ $libre ? 'btn-outline-success' : 'btn-outline-secondary' }} m-1"
                                           for="hora-{{ $loop->index }}">
                                        {{ $hora }}{{ $libre ? '' : ' · ocupada' }}
                                    </label>
                                @endforeach
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="nombre">Nombre</label>
                                    <input type="text" name="nombre" id="nombre" class="form-control" maxlength="80"
                                           value="{{ old('nombre') }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label" for="email">Correo</label>
                                    <input type="email" name="email" id="email" class="form-control" maxlength="100"
                                           value="{{ old('email') }}" required>
                                </div>
                            </div>

                            <a href="{{ route('reservas.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button class="btn btn-primary">Reservar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
