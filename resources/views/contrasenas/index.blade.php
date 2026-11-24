@extends('layouts.app')

@section('content')
    @include('partials.pagetitle', ['titulo' => 'Generador de contraseñas'])

    <section class="section dashboard">
        <div class="row">
            <div class="col-lg-5">
                <div class="card">
                    <div class="card-header"><h3>Opciones</h3></div>

                    <div class="card-body mt-3">
                        <form action="{{ route('contrasenas.generar') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label" for="longitud">
                                    Longitud: <strong id="valor-longitud">{{ old('longitud', $opciones['longitud']) }}</strong>
                                </label>
                                <input type="range" min="4" max="64" name="longitud" id="longitud" class="form-range"
                                       value="{{ old('longitud', $opciones['longitud']) }}"
                                       oninput="document.getElementById('valor-longitud').textContent = this.value">
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="cantidad">Cantidad de contraseñas</label>
                                <input type="number" min="1" max="10" name="cantidad" id="cantidad" class="form-control"
                                       value="{{ old('cantidad', $opciones['cantidad']) }}">
                            </div>

                            @php
                                $tipos = [
                                    'minusculas' => 'Minúsculas (a-z)',
                                    'mayusculas' => 'Mayúsculas (A-Z)',
                                    'numeros' => 'Números (0-9)',
                                    'simbolos' => 'Símbolos (!@#$...)',
                                    'evitar_ambiguos' => 'Evitar caracteres ambiguos (i, l, 1, L, o, 0, O)',
                                ];
                            @endphp

                            @foreach ($tipos as $campo => $texto)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="{{ $campo }}" id="{{ $campo }}" value="1"
                                           {{ ($opciones[$campo] ?? false) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="{{ $campo }}">{{ $texto }}</label>
                                </div>
                            @endforeach

                            <button class="btn btn-primary mt-3"><i class="bi bi-arrow-repeat"></i> Generar</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header"><h3>Contraseñas generadas</h3></div>

                    <div class="card-body mt-3">
                        @forelse ($contrasenas as $contrasena)
                            <div class="input-group mb-2">
                                <input type="text" class="form-control font-monospace" value="{{ $contrasena }}" readonly>
                                <button type="button" class="btn btn-outline-secondary btn-copiar" data-valor="{{ $contrasena }}">
                                    <i class="bi bi-clipboard"></i> Copiar
                                </button>
                            </div>
                        @empty
                            <p class="text-muted mb-0">Elige las opciones y pulsa "Generar".</p>
                        @endforelse

                        @if ($fortaleza)
                            <p class="mt-3 mb-0">
                                Fortaleza:
                                <span class="badge bg-{{ $fortaleza[2] }}">{{ $fortaleza[1] }}</span>
                                <small class="text-muted">(~{{ $fortaleza[0] }} bits de entropía)</small>
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-copiar').forEach(function (boton) {
            boton.addEventListener('click', function () {
                navigator.clipboard.writeText(boton.dataset.valor).then(function () {
                    Swal.fire({ title: 'Copiada al portapapeles', icon: 'success', timer: 1200, showConfirmButton: false });
                });
            });
        });
    </script>
@endpush
