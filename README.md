# Taller de seguimiento – PHP / Laravel con arquitectura MVC

Once ejercicios **independientes** hechos con PHP 8.3 y Laravel 13, siguiendo la misma estructura vista en clase
(rutas por módulo, controlador con métodos `index / create / edit / store / update / delete`, `Validator::make`
con mensajes en español, `try/catch` con `Log::error`, mensajes con `Session::flash` y SweetAlert2, plantilla NiceAdmin).

## Cómo funciona el MVC en este proyecto

| Capa | Dónde está | Qué hace |
| --- | --- | --- |
| **Modelo** | `app/Models/` y `database/migrations/` | Los datos (Eloquent) y la lógica de negocio (cálculos, reglas, informes). |
| **Vista** | `resources/views/<ejercicio>/` | Lo que ve el usuario (Blade + Bootstrap). No calcula nada importante. |
| **Controlador** | `app/Http/Controllers/<Ejercicio>Controller.php` | Recibe la petición, valida, llama al modelo y elige la vista. |
| **Rutas** | `routes/web/<ejercicio>.php` | Un archivo por ejercicio, incluido en `routes/web.php`. |

Cada ejercicio vive en sus propios archivos: se puede agregar o quitar uno sin tocar los demás
(el menú, el inicio y los seeders solo muestran lo que existe). En Git, **cada ejercicio es un commit aparte**.

## Los ejercicios

| # | Ejercicio | URL | Modelo(s) | Qué se puede hacer |
| --- | --- | --- | --- | --- |
| 1 | Lista de tareas | `/tareas` | `Tarea` | Agregar, eliminar y marcar como completadas; filtrar por estado. |
| 2 | Calculadora de propinas | `/propinas` | `Propina` (sin tabla) | Propina y total según monto y porcentaje; dividir entre personas. |
| 3 | Generador de contraseñas | `/contrasenas` | `GeneradorContrasena` (sin tabla) | Longitud, tipos de caracteres, evitar ambiguos, fortaleza, copiar. |
| 4 | Gestor de gastos | `/gastos` | `Gasto`, `CategoriaGasto` | CRUD de gastos por categoría y resumen mensual (por categoría y últimos 6 meses). |
| 5 | Sistema de reservas | `/reservas` | `Reserva`, `Servicio` | Ver disponibilidad por servicio y día, reservar, confirmar y cancelar. |
| 6 | Gestor de notas | `/notas` | `Nota`, `CategoriaNota` | CRUD de notas, categorías propias y búsqueda por texto y categoría. |
| 7 | Calendario de eventos | `/eventos` | `Evento` | Calendario mensual, CRUD de eventos, recordatorios y notificaciones. |
| 8 | Plataforma de recetas | `/recetas` | `Receta` | CRUD, buscar, filtrar (tipo, ingrediente), guardar y compartir con enlace. |
| 9 | Juego de memoria | `/memoria` | `Memoria` (sin tabla), `PartidaMemoria` | 3 niveles, puntaje calculado en el servidor y ranking. |
| 10 | Plataforma de encuestas | `/encuestas` | `Encuesta`, `Pregunta`, `Opcion`, `Participacion`, `Respuesta` | Crear, responder, cerrar; informe con gráficas, impresión y CSV. |
| 11 | Cronómetro online | `/cronometro` | `SesionCronometro` | Iniciar, pausar, reiniciar, vueltas y guardar sesiones. |

Notas de diseño:

- No hay inicio de sesión: es un taller de una sola persona, así que cada ejercicio se centra en su lógica MVC.
- Los datos importantes **se validan en el servidor**: el puntaje de la memoria, la disponibilidad de las reservas
  y las opciones de las encuestas no se confían al navegador.
- Los recordatorios del calendario funcionan mientras haya una pestaña de la aplicación abierta
  (consulta `/eventos/proximos` cada 30 s y muestra un aviso; si activas las notificaciones del navegador, también las usa).

## Instalación (Windows + WAMP, en PowerShell)

Requisitos: PHP 8.3+, Composer, MySQL (WAMP) y Git.

```powershell
git clone <URL-DEL-REPOSITORIO> taller-mvc
cd taller-mvc

composer install
copy .env.example .env
php artisan key:generate
```

1. Abre phpMyAdmin y crea una base de datos vacía llamada **`taller_mvc`** (cotejamiento `utf8mb4_unicode_ci`).
2. Si tu MySQL tiene contraseña, ponla en `DB_PASSWORD` dentro de `.env`.

```powershell
php artisan migrate --seed
php artisan serve
```

Abre <http://127.0.0.1:8000>. El `--seed` carga las categorías de gastos y de notas y los servicios de ejemplo para las reservas.

## Pruebas

```powershell
php artisan test
```

Usan SQLite en memoria (no tocan tu base MySQL). Cubren los modelos con lógica (cálculos, puntaje, informes)
y el flujo HTTP de cada ejercicio, incluidas las validaciones.

## Compartir el repositorio con el profesor

En GitHub: **Settings → Collaborators → Add people** y agrega su usuario (o deja el repositorio público durante la calificación).
