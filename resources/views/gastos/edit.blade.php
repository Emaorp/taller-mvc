@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Editar gasto', 'migas' => ['Gastos' => route('gastos.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Editar gasto</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('gastos.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="gasto_id" value="{{ $gasto->id }}">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="120"
                                   value="{{ old('descripcion', $gasto->descripcion) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria_gasto_id">Categoría</label>
                            <select name="categoria_gasto_id" id="categoria_gasto_id" class="form-select" required>
                                <option value="">Selecciona...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_gasto_id', $gasto->categoria_gasto_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="monto">Monto</label>
                            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control"
                                   value="{{ old('monto', $gasto->monto) }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control"
                                   value="{{ old('fecha', $gasto->fecha->toDateString()) }}" required>
                        </div>
                    </div>

                    <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </section>
@endsection
