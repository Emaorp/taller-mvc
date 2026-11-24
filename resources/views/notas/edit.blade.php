@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Editar nota', 'migas' => ['Notas' => route('notas.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Editar nota</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('notas.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="nota_id" value="{{ $nota->id }}">

                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label class="form-label" for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="120"
                                   value="{{ old('titulo', $nota->titulo) }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="categoria_nota_id">Categoría</label>
                            <select name="categoria_nota_id" id="categoria_nota_id" class="form-select" required>
                                <option value="">Selecciona...</option>
                                @foreach ($categorias as $categoria)
                                    <option value="{{ $categoria->id }}" {{ old('categoria_nota_id', $nota->categoria_nota_id) == $categoria->id ? 'selected' : '' }}>
                                        {{ $categoria->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label" for="contenido">Contenido</label>
                            <textarea name="contenido" id="contenido" rows="8" class="form-control" maxlength="5000" required>{{ old('contenido', $nota->contenido) }}</textarea>
                        </div>
                    </div>

                    <a href="{{ route('notas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Guardar</button>
                </form>
            </div>
        </div>
    </section>
@endsection
