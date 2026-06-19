@php
$tasks = [
    ['id' => 1, 'title' => 'Rediseñar la página de inicio', 'description' => 'Actualizar el diseño de la landing page con el nuevo estilo neumórfico y mejorar la sección de características.', 'status' => 'in-progress', 'due_date' => '2026-06-22', 'created_at' => '2026-06-15'],
    ['id' => 2, 'title' => 'Implementar autenticación', 'description' => 'Configurar login, registro y logout con validación de formularios y diseño neumórfico.', 'status' => 'completed', 'due_date' => '2026-06-14', 'created_at' => '2026-06-10'],
    ['id' => 3, 'title' => 'Crear migraciones de la BD', 'description' => 'Diseñar y ejecutar las migraciones para la tabla de tareas con SoftDeletes y timestamps.', 'status' => 'pending', 'due_date' => '2026-06-25', 'created_at' => '2026-06-16'],
    ['id' => 4, 'title' => 'Diseñar dashboard principal', 'description' => 'Crear la vista del dashboard con sidebar, estadísticas, filtros y lista de tareas.', 'status' => 'completed', 'due_date' => '2026-06-18', 'created_at' => '2026-06-12'],
    ['id' => 5, 'title' => 'Agregar animaciones GSAP', 'description' => 'Implementar animaciones suaves en el dashboard: entrada de tareas, contadores, filtros y transiciones.', 'status' => 'in-progress', 'due_date' => '2026-06-20', 'created_at' => '2026-06-17'],
    ['id' => 6, 'title' => 'Conectar con API de tareas', 'description' => 'Una vez listo el CRUD, conectar el dashboard con los endpoints para datos reales.', 'status' => 'pending', 'due_date' => '2026-06-28', 'created_at' => '2026-06-18'],
    ['id' => 7, 'title' => 'Pruebas de funcionalidad', 'description' => 'Realizar pruebas de todas las funcionalidades del sistema antes del despliegue.', 'status' => 'pending', 'due_date' => '2026-06-30', 'created_at' => '2026-06-18'],
    ['id' => 8, 'title' => 'Despliegue en producción', 'description' => 'Preparar el entorno de producción y realizar el despliegue del sistema.', 'status' => 'in-progress', 'due_date' => '2026-07-05', 'created_at' => '2026-06-18'],
];

$filterStatus = request('status');

$filteredTasks = $filterStatus && $filterStatus !== 'all'
    ? collect($tasks)->where('status', $filterStatus)->values()->all()
    : $tasks;

$stats = [
    'total' => count($tasks),
    'pending' => collect($tasks)->where('status', 'pending')->count(),
    'in_progress' => collect($tasks)->where('status', 'in-progress')->count(),
    'completed' => collect($tasks)->where('status', 'completed')->count(),
];

$statusLabels = [
    'pending' => 'Pendiente',
    'in-progress' => 'En Progreso',
    'completed' => 'Completada',
];

$pageInfo = [
    '' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
    'all' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
    'pending' => ['title' => 'Tareas Pendientes', 'subtitle' => 'Tienes ' . count($filteredTasks) . ' tareas pendientes.'],
    'in-progress' => ['title' => 'Tareas en Progreso', 'subtitle' => 'Tienes ' . count($filteredTasks) . ' tareas en progreso.'],
    'completed' => ['title' => 'Tareas Completadas', 'subtitle' => 'Tienes ' . count($filteredTasks) . ' tareas completadas.'],
];

$currentPage = $pageInfo[$filterStatus] ?? $pageInfo['all'];

$user = auth()->user();
$displayName = $user->display_name ?? ($user->first_name . ' ' . $user->last_name);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ideas En Movimiento — Task Manager</title>
    <link rel="icon" href="{{ asset('images/logo.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>(function(){var t=localStorage.getItem('theme');if(t==='dark'||(!t&&window.matchMedia('(prefers-color-scheme:dark)').matches))document.documentElement.classList.add('dark');})();</script>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/Flip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/CustomEase.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/SplitText.min.js"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="{{ asset('js/PrettyModal.js') }}" defer></script>
    <script src="{{ asset('js/dashboard.js') }}" defer></script>
</head>
<body>

    <div class="dashboard-layout">

        @include('partials.sidebar', ['stats' => $stats, 'user' => $user, 'displayName' => $displayName])

        <main class="main-content">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Ideas En Movimiento</h1>
                    <h2 class="page-subtitle">{{ $currentPage['subtitle'] }}</h2>
                </div>

            </div>

            <div class="tasks-container" id="tasks-container">
                @forelse ($filteredTasks as $task)
                <div class="task-card" data-status="{{ $task['status'] }}" data-id="{{ $task['id'] }}">
                    <div class="task-check">
                        <input type="checkbox" id="task-{{ $task['id'] }}" {{ $task['status'] === 'completed' ? 'checked' : '' }}>
                        <label for="task-{{ $task['id'] }}" class="task-check-label">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </label>
                    </div>
                    <div class="task-body">
                        <div class="task-title">{{ $task['title'] }}</div>
                        @if ($task['description'])
                        <div class="task-desc">{{ $task['description'] }}</div>
                        @endif
                        <div class="task-meta">
                            <span class="task-status {{ $task['status'] }}">{{ $statusLabels[$task['status']] ?? $task['status'] }}</span>
                            <span class="task-due {{ \Carbon\Carbon::parse($task['due_date'])->isPast() && $task['status'] !== 'completed' ? 'overdue' : '' }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <polyline points="12 6 12 12 16 14"/>
                                </svg>
                                {{ \Carbon\Carbon::parse($task['due_date'])->format('d/m/Y') }}
                            </span>
                        </div>
                    </div>
                    <div class="task-actions">
                        <button class="task-action-btn" onclick="prettyModal.open('modal-edit-task-{{ $task['id'] }}')" title="Editar tarea">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                        </button>
                        <button class="task-action-btn danger" onclick="prettyModal.open('modal-delete-task-{{ $task['id'] }}')" title="Eliminar tarea">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/>
                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                            </svg>
                        </button>
                    </div>
                </div>
                @empty
                <div class="empty-state">
                    <div class="neu-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <h3>No hay tareas aquí</h3>
                    <p>No se encontraron tareas con este filtro.</p>
                    <a href="{{ route('tasks') }}" class="btn-neu">Ver todas las tareas</a>
                </div>
                @endforelse
            </div>
        </main>
    </div>

    @includeWhen(isset($includeTaskModals) && $includeTaskModals, 'partials.task-modals')

    <dialog id="modal-create-task" class="neu-modal">
        <div class="neu-modal-content">
            <div class="neu-modal-header">
                <h2>Nueva Tarea</h2>
                <button class="neu-modal-close" onclick="prettyModal.close('modal-create-task')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form class="modal-form">
                <div class="form-group">
                    <label for="create-title">Título</label>
                    <input type="text" id="create-title" name="title" placeholder="Nombre de la tarea" required>
                </div>
                <div class="form-group">
                    <label for="create-description">Descripción</label>
                    <textarea id="create-description" name="description" placeholder="Describe los detalles de la tarea..."></textarea>
                </div>
                <div class="form-group">
                    <label for="create-status">Estado</label>
                    <select id="create-status" name="status">
                        <option value="pending">Pendiente</option>
                        <option value="in-progress">En Progreso</option>
                        <option value="completed">Completada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="create-due_date">Fecha Límite</label>
                    <input type="date" id="create-due_date" name="due_date">
                </div>
            </form>
            <div class="neu-modal-footer">
                <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-create-task')">Cancelar</button>
                <button class="btn-neu btn-neu-sm btn-neu-primary">Crear Tarea</button>
            </div>
        </div>
    </dialog>

    @foreach ($tasks as $task)
    <dialog id="modal-edit-task-{{ $task['id'] }}" class="neu-modal">
        <div class="neu-modal-content">
            <div class="neu-modal-header">
                <h2>Editar Tarea</h2>
                <button class="neu-modal-close" onclick="prettyModal.close('modal-edit-task-{{ $task['id'] }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form class="modal-form">
                <div class="form-group">
                    <label for="edit-title-{{ $task['id'] }}">Título</label>
                    <input type="text" id="edit-title-{{ $task['id'] }}" name="title" value="{{ $task['title'] }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-description-{{ $task['id'] }}">Descripción</label>
                    <textarea id="edit-description-{{ $task['id'] }}" name="description">{{ $task['description'] }}</textarea>
                </div>
                <div class="form-group">
                    <label for="edit-status-{{ $task['id'] }}">Estado</label>
                    <select id="edit-status-{{ $task['id'] }}" name="status">
                        <option value="pending" {{ $task['status'] === 'pending' ? 'selected' : '' }}>Pendiente</option>
                        <option value="in-progress" {{ $task['status'] === 'in-progress' ? 'selected' : '' }}>En Progreso</option>
                        <option value="completed" {{ $task['status'] === 'completed' ? 'selected' : '' }}>Completada</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-due_date-{{ $task['id'] }}">Fecha Límite</label>
                    <input type="date" id="edit-due_date-{{ $task['id'] }}" name="due_date" value="{{ $task['due_date'] }}">
                </div>
            </form>
            <div class="neu-modal-footer">
                <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-edit-task-{{ $task['id'] }}')">Cancelar</button>
                <button class="btn-neu btn-neu-sm btn-neu-primary">Guardar Cambios</button>
            </div>
        </div>
    </dialog>

    <dialog id="modal-delete-task-{{ $task['id'] }}" class="neu-modal">
        <div class="neu-modal-content" style="text-align:center;">
            <div class="delete-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
            </div>
            <div class="neu-modal-header" style="justify-content:center;margin-bottom:16px;">
                <h2>Eliminar Tarea</h2>
            </div>
            <div class="delete-text">¿Estás seguro de que deseas eliminar esta tarea?</div>
            <div class="delete-task-title">"{{ $task['title'] }}"</div>
            <div class="neu-modal-footer" style="justify-content:center;">
                <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-delete-task-{{ $task['id'] }}')">Cancelar</button>
                <button class="btn-neu btn-neu-sm btn-neu-danger">Eliminar</button>
            </div>
        </div>
    </dialog>
    @endforeach

</body>
</html>
