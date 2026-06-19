@php
$user = auth()->user();
$displayName = $user->display_name ?? ($user->first_name . ' ' . $user->last_name);

$tasks = App\Models\Task::with('status')
    ->where('user_id', $user->id)
    ->latest()
    ->take(8)
    ->get();

$stats = [
    'total' => App\Models\Task::where('user_id', $user->id)->count(),
    'pending' => App\Models\Task::where('user_id', $user->id)->whereHas('status', fn($q) => $q->where('name', 'pending'))->count(),
    'in_progress' => App\Models\Task::where('user_id', $user->id)->whereHas('status', fn($q) => $q->where('name', 'in-progress'))->count(),
    'completed' => App\Models\Task::where('user_id', $user->id)->whereHas('status', fn($q) => $q->where('name', 'completed'))->count(),
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
    $count = App\Models\Task::where('user_id', $user->id)
        ->whereHas('status', fn($q) => $q->where('name', $st))
        ->count();
    if ($count > 0) {
        $chartDonut[] = ['name' => $chartLabels[$st], 'y' => $count, 'colorKey' => $st];
    }
}

$dayLabels = ['Mon' => 'Lun', 'Tue' => 'Mar', 'Wed' => 'Mié', 'Thu' => 'Jue', 'Fri' => 'Vie', 'Sat' => 'Sáb', 'Sun' => 'Dom'];
$dayOrder = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

$weeklyData = [];
foreach ($tasks as $task) {
    $day = $task->created_at->format('D');
    if (!isset($weeklyData[$day])) $weeklyData[$day] = ['pending' => 0, 'in-progress' => 0, 'completed' => 0];
    $weeklyData[$day][$task->status->name]++;
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
    $month = $task->created_at->format('M Y');
    if (!isset($monthlyData[$month])) $monthlyData[$month] = ['pending' => 0, 'in-progress' => 0, 'completed' => 0];
    $monthlyData[$month][$task->status->name]++;
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

$taskStates = App\Models\TaskState::all();
$priorityTypes = App\Models\PriorityType::all();
$statusNameToLabel = [
    'pending' => 'Pendiente',
    'in-progress' => 'En Progreso',
    'completed' => 'Completada',
];
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
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/Flip.min.js" integrity="sha384-LY8cG/IUULu4u3V3AhwWBt01HIuO/hlekjkqgBx0DOJ/oquEL0Qk2L6qy+1QeRZM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/CustomEase.min.js" integrity="sha384-bk/dsRkKcZYqsQ8OzP86S+TVAAI6D7V0ApKLhj3ssXqZPNYYO77EXxOrTX+pp1g/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/SplitText.min.js" integrity="sha384-SWJ0lLVRoipvHh59xj0pL7uC7Ih51F+5smaFtrG+2nr+TlDZU5SYJHmxfolbeNTr" crossorigin="anonymous"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js" integrity="sha384-IqnB/DHWJQQpZ3OIfSLVFhTYsWMP8rEgre/UhDirnQcKtiu6wtA0WltNGOfET0ql" crossorigin="anonymous"></script>
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

    @include('partials.create-task-modal')



</body>
</html>
