@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Crear encuesta', 'migas' => ['Encuestas' => route('encuestas.index')]])

    @php
        $preguntas = old('preguntas', [ ['texto' => '', 'opciones' => ['', '']] ]);
    @endphp

    <section class="section dashboard">
        <div class="card">
            <div class="card-header"><h3>Nueva encuesta</h3></div>

            <div class="card-body mt-3">
                <form action="{{ route('encuestas.store') }}" method="POST">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="titulo">Título</label>
                            <input type="text" name="titulo" id="titulo" class="form-control" maxlength="120"
                                   value="{{ old('titulo') }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label" for="descripcion">Descripción (opcional)</label>
                            <input type="text" name="descripcion" id="descripcion" class="form-control" maxlength="300"
                                   value="{{ old('descripcion') }}">
                        </div>
                    </div>

                    <h5>Preguntas</h5>
                    <div id="preguntas"></div>

                    <button type="button" id="agregar-pregunta" class="btn btn-outline-primary mb-3">
                        <i class="bi bi-plus-lg"></i> Agregar pregunta
                    </button>

                    <div>
                        <a href="{{ route('encuestas.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button class="btn btn-primary">Guardar encuesta</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        (function () {
            const contenedor = document.getElementById('preguntas');
            const inicial = @json(array_values($preguntas));
            let contador = 0;

            function escapar(t) {
                const d = document.createElement('div');
                d.textContent = t || '';
                return d.innerHTML.replace(/"/g, '&quot;');
            }

            function agregarOpcion(lista, indice, valor) {
                const fila = document.createElement('div');
                fila.className = 'input-group mb-2';
                fila.innerHTML =
                    '<input type="text" class="form-control" maxlength="100" placeholder="Opción" name="preguntas[' + indice + '][opciones][]" value="' + escapar(valor) + '">' +
                    '<button type="button" class="btn btn-outline-danger quitar-opcion"><i class="bi bi-x-lg"></i></button>';
                fila.querySelector('.quitar-opcion').addEventListener('click', function () { fila.remove(); });
                lista.appendChild(fila);
            }

            function agregarPregunta(datos) {
                const indice = contador++;
                const caja = document.createElement('div');
                caja.className = 'card border mb-3';
                caja.innerHTML =
                    '<div class="card-body mt-3">' +
                        '<div class="input-group mb-3">' +
                            '<input type="text" class="form-control" maxlength="200" placeholder="Texto de la pregunta" name="preguntas[' + indice + '][texto]" value="' + escapar(datos.texto) + '">' +
                            '<button type="button" class="btn btn-danger quitar-pregunta" title="Quitar pregunta"><i class="bi bi-trash-fill"></i></button>' +
                        '</div>' +
                        '<div class="opciones"></div>' +
                        '<button type="button" class="btn btn-sm btn-outline-secondary agregar-opcion"><i class="bi bi-plus"></i> Agregar opción</button>' +
                    '</div>';

                const lista = caja.querySelector('.opciones');
                (datos.opciones && datos.opciones.length ? datos.opciones : ['', '']).forEach(function (o) { agregarOpcion(lista, indice, o); });

                caja.querySelector('.agregar-opcion').addEventListener('click', function () { agregarOpcion(lista, indice, ''); });
                caja.querySelector('.quitar-pregunta').addEventListener('click', function () {
                    if (contenedor.children.length > 1) caja.remove();
                });

                contenedor.appendChild(caja);
            }

            document.getElementById('agregar-pregunta').addEventListener('click', function () {
                agregarPregunta({ texto: '', opciones: ['', ''] });
            });

            inicial.forEach(agregarPregunta);
        })();
    </script>
@endpush
