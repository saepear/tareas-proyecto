@php
$stats = [
    'total' => 8,
    'pending' => 3,
    'in_progress' => 3,
    'completed' => 2,
];

$tasks = [
    [
        'id' => 1,
        'title' => 'Rediseñar la página de inicio',
        'description' => 'Actualizar el diseño de la landing page con el nuevo estilo neumórfico y mejorar la sección de características.',
        'status' => 'in-progress',
        'due_date' => '2026-06-22',
        'created_at' => '2026-06-15',
    ],
    [
        'id' => 2,
        'title' => 'Implementar autenticación',
        'description' => 'Configurar login, registro y logout con validación de formularios y diseño neumórfico.',
        'status' => 'completed',
        'due_date' => '2026-06-14',
        'created_at' => '2026-06-10',
    ],
    [
        'id' => 3,
        'title' => 'Crear migraciones de la BD',
        'description' => 'Diseñar y ejecutar las migraciones para la tabla de tareas con SoftDeletes y timestamps.',
        'status' => 'pending',
        'due_date' => '2026-06-25',
        'created_at' => '2026-06-16',
    ],
    [
        'id' => 4,
        'title' => 'Diseñar dashboard principal',
        'description' => 'Crear la vista del dashboard con sidebar, estadísticas, filtros y lista de tareas.',
        'status' => 'completed',
        'due_date' => '2026-06-18',
        'created_at' => '2026-06-12',
    ],
    [
        'id' => 5,
        'title' => 'Agregar animaciones GSAP',
        'description' => 'Implementar animaciones suaves en el dashboard: entrada de tareas, contadores, filtros y transiciones.',
        'status' => 'in-progress',
        'due_date' => '2026-06-20',
        'created_at' => '2026-06-17',
    ],
    [
        'id' => 6,
        'title' => 'Conectar con API de tareas',
        'description' => 'Una vez listo el CRUD, conectar el dashboard con los endpoints para datos reales.',
        'status' => 'pending',
        'due_date' => '2026-06-28',
        'created_at' => '2026-06-18',
    ],
    [
        'id' => 7,
        'title' => 'Pruebas de funcionalidad',
        'description' => 'Realizar pruebas de todas las funcionalidades del sistema antes del despliegue.',
        'status' => 'pending',
        'due_date' => '2026-06-30',
        'created_at' => '2026-06-18',
    ],
    [
        'id' => 8,
        'title' => 'Despliegue en producción',
        'description' => 'Preparar el entorno de producción y realizar el despliegue del sistema.',
        'status' => 'in-progress',
        'due_date' => '2026-07-05',
        'created_at' => '2026-06-18',
    ],
];

$statusLabels = [
    'pending' => 'Pendiente',
    'in-progress' => 'En Progreso',
    'completed' => 'Completada',
];

$chartLabels = [
    'pending' => 'Pendientes',
    'in-progress' => 'En Progreso',
    'completed' => 'Completadas',
];

$chartDonut = [];
foreach (['pending', 'in-progress', 'completed'] as $st) {
    $count = collect($tasks)->where('status', $st)->count();
    if ($count > 0) {
        $chartDonut[] = ['name' => $chartLabels[$st], 'y' => $count, 'colorKey' => $st];
    }
}

$dayLabels = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb', 'Sun' => 'Dom'];
$dayOrder = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
$weeklyData = [];
foreach ($tasks as $task) {
    $day = \Carbon\Carbon::parse($task['created_at'])->format('D');
    if (!isset($weeklyData[$day])) $weeklyData[$day] = ['pending' => 0, 'in-progress' => 0, 'completed' => 0];
    $weeklyData[$day][$task['status']]++;
}
uksort($weeklyData, function ($a, $b) use ($dayOrder) {
    return array_search($a, $dayOrder) - array_search($b, $dayOrder);
});
$chartWeeklyCategories = [];
$chartWeeklySeries = [
    ['name' => 'Pendientes', 'data' => [], 'colorKey' => 'pending'],
    ['name' => 'En Progreso', 'data' => [], 'colorKey' => 'in-progress'],
    ['name' => 'Completadas', 'data' => [], 'colorKey' => 'completed'],
];
foreach ($weeklyData as $day => $counts) {
    $chartWeeklyCategories[] = $dayLabels[$day] ?? $day;
    $chartWeeklySeries[0]['data'][] = $counts['pending'];
    $chartWeeklySeries[1]['data'][] = $counts['in-progress'];
    $chartWeeklySeries[2]['data'][] = $counts['completed'];
}

$monthlyData = [];
foreach ($tasks as $task) {
    $month = \Carbon\Carbon::parse($task['created_at'])->format('M Y');
    if (!isset($monthlyData[$month])) $monthlyData[$month] = ['pending' => 0, 'in-progress' => 0, 'completed' => 0];
    $monthlyData[$month][$task['status']]++;
}
ksort($monthlyData);
$chartMonthlyCategories = [];
$chartMonthlySeries = [
    ['name' => 'Pendientes', 'data' => [], 'colorKey' => 'pending'],
    ['name' => 'En Progreso', 'data' => [], 'colorKey' => 'in-progress'],
    ['name' => 'Completadas', 'data' => [], 'colorKey' => 'completed'],
];
foreach ($monthlyData as $month => $counts) {
    $chartMonthlyCategories[] = $month;
    $chartMonthlySeries[0]['data'][] = $counts['pending'];
    $chartMonthlySeries[1]['data'][] = $counts['in-progress'];
    $chartMonthlySeries[2]['data'][] = $counts['completed'];
}

$chartData = [
    'donut' => $chartDonut,
    'weekly' => ['categories' => $chartWeeklyCategories, 'series' => $chartWeeklySeries],
    'monthly' => ['categories' => $chartMonthlyCategories, 'series' => $chartMonthlySeries],
];

$user = auth()->user();
$displayName = $user->display_name ?? ($user->first_name . ' ' . $user->last_name);
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Task Manager</title>
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
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script>window.CHART_DATA = @json($chartData);</script>
    <script src="{{ asset('js/charts.js') }}" defer></script>
    <script src="{{ asset('js/PrettyModal.js') }}" defer></script>
    <script src="{{ asset('js/dashboard.js') }}" defer></script>

</head>
<body>

    <div class="dashboard-layout">

        @include('partials.sidebar', ['stats' => $stats, 'user' => $user, 'displayName' => $displayName])

        <main class="main-content">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Bienvenido {{ $displayName }}</h1>
                    <h2 class="page-subtitle">Tienes {{ $stats['pending'] }} tareas pendientes.</h2>
                </div>
            </div>

            <div class="charts-stack">
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Distribución de Tareas</h3>
                    </div>
                    <div id="chart-donut" class="chart-container"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Tareas por Día de la Semana</h3>
                    </div>
                    <div id="chart-weekly" class="chart-container"></div>
                </div>
                <div class="chart-card">
                    <div class="chart-card-header">
                        <h3>Tareas por Mes</h3>
                    </div>
                    <div id="chart-monthly" class="chart-container"></div>
                </div>
            </div>


        </main>
    </div>

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



</body>
</html>
