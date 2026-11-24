@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Gestor de notas'])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-8"><h3>Mis notas</h3></div>
                    <div class="col-md-4 text-end">
                        <a href="{{ route('notas.categorias') }}" class="btn btn-info"><i class="bi bi-tags-fill"></i> Categorías</a>
                        <a href="{{ route('notas.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle-fill"></i> Nueva nota</a>
                    </div>
                </div>
            </div>

            <div class="card-body mt-3">

                <form action="{{ route('notas.index') }}" method="GET" class="row g-2 mb-3">
                    <div class="col-md-6">
                        <input type="text" name="filter" class="form-control" placeholder="Buscar en título o contenido..."
                               value="{{ $data->filter }}">
                    </div>
                    <div class="col-md-4">
                        <select name="categoria_id" class="form-select">
                            <option value="">Todas las categorías</option>
                            @foreach ($categorias as $categoria)
                                <option value="{{ $categoria->id }}" {{ $data->categoria_id == $categoria->id ? 'selected' : '' }}>
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-auto">
                        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
                    </div>
                </form>

                <div class="row">
                    @forelse ($notas as $nota)
                        <div class="col-md-6 col-xl-4">
                            <div class="card border">
                                <div class="card-body">
                                    <h5 class="card-title">{{ $nota->titulo }}</h5>
                                    <span class="badge bg-primary mb-2">{{ $nota->categoria->nombre }}</span>
                                    <p class="small">{{ \Illuminate\Support\Str::limit($nota->contenido, 140) }}</p>
                                    <small class="text-muted d-block mb-2">{{ $nota->updated_at->format('d/m/Y H:i') }}</small>

                                    <a href="{{ route('notas.edit', $nota->id) }}" class="btn btn-warning btn-sm">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                    <form action="{{ route('notas.delete', $nota->id) }}" method="POST"
                                          class="d-inline form-eliminar" data-mensaje="¿Eliminar la nota?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">No se encontraron notas.</p>
                    @endforelse
                </div>

                {{ $notas->links() }}
            </div>
        </div>
    </section>
@endsection
