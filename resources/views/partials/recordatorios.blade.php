<script>
    (function () {
        const url = @json(route('eventos.proximos'));

        function avisados() {
            try { return JSON.parse(localStorage.getItem('eventos_avisados') || '{}'); } catch (e) { return {}; }
        }

        function marcar(clave) {
            try {
                const a = avisados();
                a[clave] = Date.now();
                localStorage.setItem('eventos_avisados', JSON.stringify(a));
            } catch (e) {}
        }
        function mostrarAviso(texto) {
            let caja = document.getElementById('avisos-recordatorio');

            if (!caja) {
                caja = document.createElement('div');
                caja.id = 'avisos-recordatorio';
                caja.style.cssText = 'position:fixed;top:70px;right:16px;z-index:2000;display:flex;flex-direction:column;gap:8px;';
                document.body.appendChild(caja);
            }

            const aviso = document.createElement('div');
            aviso.className = 'alert alert-info shadow d-flex align-items-center mb-0';
            aviso.setAttribute('role', 'alert');
            aviso.innerHTML = '<i class="bi bi-bell-fill me-2"></i><div class="me-3"></div>' +
                              '<button type="button" class="btn-close ms-auto" aria-label="Cerrar"></button>';
            aviso.querySelector('div').textContent = 'Recordatorio: ' + texto;
            aviso.querySelector('button').addEventListener('click', function () { aviso.remove(); });
            caja.appendChild(aviso);

            setTimeout(function () { aviso.remove(); }, 15000);
        }

        function notificar(ev) {
            const faltan = Math.max(0, Math.round((new Date(ev.inicio).getTime() - Date.now()) / 60000));
            const texto = faltan > 0 ? ev.titulo + ' empieza en ' + faltan + ' min' : ev.titulo + ' está empezando';

            mostrarAviso(texto);

            if (window.Notification && Notification.permission === 'granted') {
                new Notification('Recordatorio: ' + ev.titulo, { body: texto });
            }
        }

        function revisar() {
            fetch(url, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (lista) {
                    const ahora = Date.now();

                    lista.forEach(function (ev) {
                        const inicio = new Date(ev.inicio).getTime();
                        const aviso = inicio - ev.recordatorio_min * 60000;
                        const clave = ev.id + '-' + ev.inicio;

                        if (ahora >= aviso && ahora < inicio + 300000 && !avisados()[clave]) {
                            marcar(clave);
                            notificar(ev);
                        }
                    });
                })
                .catch(function () {});
        }

        revisar();
        setInterval(revisar, 30000);
    })();
</script>
