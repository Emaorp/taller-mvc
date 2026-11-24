<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

<script src="{{ asset('libs/jquery/jquery.js') }}"></script>
<script src="{{ asset('libs/sweetalert2/sweetalert2.js') }}"></script>

@if (session()->has('message'))
    <script>
        const message = @json(session('message'));

        Swal.fire({
            title: message.content,
            icon: message.type,
        });
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire({
            title: "Revisa los datos",
            icon: "error",
            html: `<ul class="list-unstyled mb-0">@foreach ($errors->all() as $error)<li class="alert alert-danger py-1">{{ $error }}</li>@endforeach</ul>`
        });
    </script>
@endif

<script>
    document.addEventListener('submit', function (e) {
        const form = e.target;

        if (form.classList && form.classList.contains('form-eliminar') && !form.dataset.confirmado) {
            e.preventDefault();

            Swal.fire({
                title: form.dataset.mensaje || '¿Eliminar este registro?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(function (r) {
                if (r.isConfirmed) {
                    form.dataset.confirmado = '1';
                    form.submit();
                }
            });
        }
    });
</script>
@includeIf('partials.recordatorios')
