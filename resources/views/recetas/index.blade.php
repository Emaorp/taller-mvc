@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Plataforma de recetas'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-10"><h3>Recetas</h3></div>
                    <div class="col-md-2 text-end">
                        <a href="{{ route('recetas.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Nueva</a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('recetas.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <input type="text" name="filter" class="form-control" placeholder="Buscar por título..."
                               value="{{ $data->filter }}">
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="ingrediente" class="form-control" placeholder="Con el ingrediente..."
                               value="{{ $data->ingrediente }}">
                    </div>
                    <div class="col-md-2">
                        <select name="tipo_comida" class="form-select">
                            <option value="">Todos los tipos</option>
                            @foreach ($tipos as $tipo)
                                <option value="{{ $tipo }}" {{ $data->tipo_comida == $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 align-self-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="solo_guardadas" id="solo_guardadas" value="1"
                                   {{ $data->boolean('solo_guardadas') ? 'checked' : '' }}>
                            <label class="form-check-label" for="solo_guardadas">Solo guardadas</label>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="row">
                    @forelse ($recetas as $receta)
                        <div class="col-md-6 col-xl-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $receta->titulo }}</h5>
                                    <span class="badge bg-primary">{{ $receta->tipo_comida }}</span>
                                    <span class="badge bg-secondary">{{ $receta->tiempo_min }} min</span>
                                    <span class="badge bg-secondary">{{ $receta->porciones }} porc.</span>
                                    <p class="small mt-2 mb-2">
                                        <strong>Ingredientes:</strong>
                                        {{ \Illuminate\Support\Str::limit(implode(', ', $receta->listaIngredientes()), 90) }}
                                    </p>

                                    <a href="{{ route('recetas.compartida', $receta->token) }}" class="btn btn-info btn-sm" title="Ver">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    <form action="{{ route('recetas.guardar', $receta->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm {{ $receta->guardada ? 'btn-warning' : 'btn-outline-warning' }}"
                                                title="{{ $receta->guardada ? 'Quitar de guardadas' : 'Guardar' }}">
                                            <i class="bi {{ $receta->guardada ? 'bi-bookmark-fill' : 'bi-bookmark' }}"></i>
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-success btn-sm btn-compartir"
                                            data-url="{{ route('recetas.compartida', $receta->token) }}" title="Copiar enlace para compartir">
                                        <i class="bi bi-share-fill"></i>
                                    </button>

                                    <a href="{{ route('recetas.edit', $receta->id) }}" class="btn btn-secondary btn-sm" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>

                                    <form action="{{ route('recetas.delete', $receta->id) }}" method="POST"
                                          class="d-inline form-eliminar" data-mensaje="¿Eliminar la receta?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">No se encontraron recetas.</p>
                    @endforelse
                </div>

                {{ $recetas->links() }}
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-compartir').forEach(function (boton) {
            boton.addEventListener('click', function () {
                navigator.clipboard.writeText(boton.dataset.url).then(function () {
                    Swal.fire({ title: 'Enlace copiado', text: boton.dataset.url, icon: 'success' });
                });
            });
        });
    </script>
@endpush
