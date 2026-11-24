@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Responder encuesta', 'migas' => ['Encuestas' => route('encuestas.index')]])

    <section class="section dashboard">
        <div class="card">
            <div class="card-header">
                <h3>{{ $encuesta->titulo }}</h3>
                @if ($encuesta->descripcion)<p class="text-muted mb-0">{{ $encuesta->descripcion }}</p>@endif
            </div>

            <div class="card-body mt-3">
                <form action="{{ route('encuestas.responder.guardar', $encuesta->id) }}" method="POST">
                    @csrf

                    @foreach ($encuesta->preguntas as $pregunta)
                        <div class="mb-4">
                            <h5>{{ $loop->iteration }}. {{ $pregunta->texto }}</h5>

                            @foreach ($pregunta->opciones as $opcion)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="respuestas[{{ $pregunta->id }}]"
                                           id="op-{{ $opcion->id }}" value="{{ $opcion->id }}"
                                           {{ old("respuestas.{$pregunta->id}") == $opcion->id ? 'checked' : '' }}>
                                    <label class="form-check-label" for="op-{{ $opcion->id }}">{{ $opcion->texto }}</label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <a href="{{ route('encuestas.index') }}" class="btn btn-secondary">Cancelar</a>
                    <button class="btn btn-primary">Enviar respuestas</button>
                </form>
            </div>
        </div>
    </section>
@endsection
