@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => $receta->titulo, 'migas' => ['Recetas' => route('recetas.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-body mt-3">
                <h3>{{ $receta->titulo }}</h3>
                <p>
                    <span class="badge bg-primary">{{ $receta->tipo_comida }}</span>
                    <span class="badge bg-secondary">{{ $receta->tiempo_min }} min</span>
                    <span class="badge bg-secondary">{{ $receta->porciones }} porciones</span>
                </p>

                <div class="row">
                    <div class="col-md-4">
                        <h5>Ingredientes</h5>
                        <ul>
                            @foreach ($receta->listaIngredientes() as $ingrediente)
                                <li>{{ $ingrediente }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="col-md-8">
                        <h5>Preparación</h5>
                        <p style="white-space: pre-line">{{ $receta->preparacion }}</p>
                    </div>
                </div>

                <a href="{{ route('recetas.index') }}" class="btn btn-secondary">Volver a las recetas</a>
            </div>
        </div>
    </section>
@endsection
