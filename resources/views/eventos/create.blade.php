@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Nuevo evento', 'migas' => ['Eventos' => route('eventos.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Nuevo evento</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('eventos.store') }}" method="POST">
                    @csrf
                    
                    

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="100"
                                   value="{{ old('titulo') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control" value="{{ old('fecha', $fecha) }}" required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label" for="descripcion">Descripción (opcional)</label>
                            <textarea name="descripcion" id="descripcion" rows="3" class="form-control" maxlength="500">{{ old('descripcion') }}</textarea>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="hora">Hora (opcional)</label>
                            <input type="time" name="hora" id="hora" class="form-control" value="{{ old('hora') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="recordatorio_min">Recordatorio</label>
                            <select name="recordatorio_min" id="recordatorio_min" class="form-select">
                                <option value="">Sin recordatorio</option>
                                @foreach ($recordatorios as $minutos => $texto)
                                    <option value="{{ $minutos }}" {{ old('recordatorio_min') == $minutos ? 'selected' : '' }}>{{ $texto }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Para usar un recordatorio, indica la hora del evento.</div>
                        </div>
                    </div>

                    <a href="{{ route('eventos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                    
                </form>
            </div>
        </div>
    </section>
@endsection
