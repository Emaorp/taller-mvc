@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Nueva receta', 'migas' => ['Recetas' => route('recetas.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Nueva receta</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('recetas.store') }}" method="POST">
                    @csrf
                    
                    

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="120" value="{{ old('titulo') }}" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="tipo_comida">Tipo de comida</label>
                            <select name="tipo_comida" id="tipo_comida" class="form-select" required>
                                <option value="">Selecciona...</option>
                                @foreach ($tipos as $tipo)
                                    <option value="{{ $tipo }}" {{ old('tipo_comida') == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="tiempo_min">Tiempo (min)</label>
                            <input type="number" min="1" max="1440" name="tiempo_min" id="tiempo_min" class="form-control" value="{{ old('tiempo_min') }}" required>
                        </div>

                        <div class="col-md-2 mb-3">
                            <label class="form-label" for="porciones">Porciones</label>
                            <input type="number" min="1" max="50" name="porciones" id="porciones" class="form-control" value="{{ old('porciones', 4) }}" required>
                        </div>

                        <div class="col-md-5 mb-3">
                            <label class="form-label" for="ingredientes">Ingredientes (uno por línea)</label>
                            <textarea name="ingredientes" id="ingredientes" rows="9" class="form-control" required>{{ old('ingredientes') }}</textarea>
                        </div>

                        <div class="col-md-7 mb-3">
                            <label class="form-label" for="preparacion">Preparación</label>
                            <textarea name="preparacion" id="preparacion" rows="9" class="form-control" required>{{ old('preparacion') }}</textarea>
                        </div>
                    </div>

                    <a href="{{ route('recetas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </section>
@endsection
