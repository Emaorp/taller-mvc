@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Categorías de notas', 'migas' => ['Notas' => route('notas.index')]])

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3>Nueva categoría</h3></div>
                    <div class="card-body mt-3">
                        <form action="{{ route('notas.categorias.store') }}" method="POST" class="row g-2">
                            @csrf
                            <div class="col">
                                <input type="text" name="nombre" class="form-control" maxlength="40" placeholder="Nombre" required>
                            </div>
                            <div class="col-auto"><button class="btn btn-primary">Agregar</button></div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3>Categorías</h3></div>
                    <div class="card-body mt-3">
                        <table class="table table-bordered align-middle">
                            <thead><tr><th>Nombre</th><th>Notas</th><th></th></tr></thead>
                            <tbody>
                                @foreach ($categorias as $categoria)
                                    <tr>
                                        <td>{{ $categoria->nombre }}</td>
                                        <td>{{ $categoria->notas_count }}</td>
                                        <td>
                                            <form action="{{ route('notas.categorias.delete', $categoria->id) }}" method="POST"
                                                  class="form-eliminar" data-mensaje="¿Eliminar la categoría?">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i class="bi bi-trash-fill"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
