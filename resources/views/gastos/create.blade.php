@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Nuevo gasto', 'migas' => ['Gastos' => route('gastos.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Nuevo gasto</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('gastos.store') }}" method="POST">
                    @csrf
                    
                    

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="descripcion">Descripción</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="120"
                                   value="{{ old('descripcion') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="categoria_gasto_id">Categoría</label>
                            <select name="categoria_gasto_id" id="categoria_gasto_id" class="form-select" required>
                                <option value="">Selecciona...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_gasto_id') == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="monto">Monto</label>
                            <input type="number" step="0.01" min="0.01" name="monto" id="monto" class="form-control"
                                   value="{{ old('monto') }}" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="fecha">Fecha</label>
                            <input type="date" name="fecha" id="fecha" class="form-control"
                                   value="{{ old('fecha', now()->toDateString()) }}" required>
                        </div>
                    </div>

                    <a href="{{ route('gastos.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </section>
@endsection
