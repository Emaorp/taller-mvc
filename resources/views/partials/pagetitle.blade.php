<div class="pagetitle">
    <h1>{{ $titulo }}</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Inicio</a></li>
            @foreach (($migas ?? []) as $texto => $url)
                <li class="breadcrumb-item"><a href="{{ $url }}">{{ $texto }}</a></li>
            @endforeach
            <li class="breadcrumb-item active">{{ $titulo }}</li>
        </ol>
    </nav>
</div>
