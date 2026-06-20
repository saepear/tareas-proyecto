@php
$user = auth()->user();
$displayName = $user->display_name ?: ($user->first_name . ' ' . $user->last_name);

$tasks = App\Models\Task::with(['status', 'priority'])
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

$combinedStats = [];
foreach (['pending', 'in-progress', 'completed'] as $st) {
    foreach (['alta', 'media', 'baja'] as $pr) {
        $combinedStats[$st][$pr] = $tasks
            ->filter(fn($t) => $t->status->name === $st && $t->priority->name === $pr)
            ->count();
    }
}

$hasTasks = $stats['total'] > 0;

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

$weekStart = now()->startOfWeek();
$weekEnd = now()->endOfWeek();

$weeklyTasks = App\Models\Task::with('status')
    ->where('user_id', $user->id)
    ->whereBetween('created_at', [$weekStart, $weekEnd])
    ->get();

$weeklyRawTasks = $weeklyTasks->map(fn ($t) => [
    'created_at' => $t->created_at->toIso8601String(),
    'status' => $t->status->name,
])->values();

$monthlyTasks = App\Models\Task::with('status')
    ->where('user_id', $user->id)
    ->get();

$monthlyData = [];
foreach ($monthlyTasks as $task) {
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
    'weeklyRaw' => $weeklyRawTasks,
    'monthly' => ['categories' => $chartMonthlyCategories, 'series' => $chartMonthlySeries],
];

$taskStates = App\Models\TaskState::all();
$editableStates = $taskStates->reject(fn($s) => $s->name === 'completed');
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
    <script>(function(){var p=window.matchMedia('(prefers-color-scheme:dark)').matches;if(p)document.documentElement.classList.add('dark');var c=localStorage.getItem('settingsSelectedColor');if(c){var m={1:'ocean',2:'sunset',3:'forest',4:'lavender',5:'rose',6:'amber',7:'slate',8:'teal',9:'berry',10:'sky'};document.documentElement.setAttribute('data-theme',m[c]||c)}var n=localStorage.getItem('settingsSelectedNavStyle');if(n&&n!=='none')document.documentElement.setAttribute('data-nav-style',n)})();</script>
    <style>body{background:#e0e5ec;margin:0}html.dark body{background:#2a2d35}</style>
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/gsap.min.js" integrity="sha384-XmJ9SoHtVOHoQUcKvFAzVXwdkKo1Ie3bhmSoIAkcdsHGaIrVJIkmozyq0FJeb/Ly" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/Flip.min.js" integrity="sha384-LY8cG/IUULu4u3V3AhwWBt01HIuO/hlekjkqgBx0DOJ/oquEL0Qk2L6qy+1QeRZM" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/CustomEase.min.js" integrity="sha384-bk/dsRkKcZYqsQ8OzP86S+TVAAI6D7V0ApKLhj3ssXqZPNYYO77EXxOrTX+pp1g/" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/gsap@3.15.0/dist/SplitText.min.js" integrity="sha384-SWJ0lLVRoipvHh59xj0pL7uC7Ih51F+5smaFtrG+2nr+TlDZU5SYJHmxfolbeNTr" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios@1.7.9/dist/axios.min.js"></script>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.1/cropper.min.js"></script>
    @if ($hasTasks)
    <script>window.CHART_DATA = @json($chartData);</script>
    @endif
    <script src="{{ asset('js/charts.js') }}" defer></script>
    <script src="{{ asset('js/PrettyModal.js') }}" defer></script>
    <script src="{{ asset('js/settings-tabs.js') }}" defer></script>
    <script src="{{ asset('js/dashboard.js') }}" defer></script>

</head>
<body>

    <div class="bg-bubbles" aria-hidden="true">
        <div class="bubble bubble-1"></div>
        <div class="bubble bubble-2"></div>
        <div class="bubble bubble-3"></div>
    </div>

    <div class="dashboard-layout">

        @include('partials.sidebar', ['stats' => $stats, 'combinedStats' => $combinedStats, 'user' => $user, 'displayName' => $displayName])
        <script>
        (function(){
            var raw;
            try { raw = sessionStorage.getItem('sidebarState'); } catch(e) {}
            if (!raw) return;
            var state;
            try { state = JSON.parse(raw); } catch(e) { return; }
            sessionStorage.removeItem('sidebarState');
            if (state.tasksOpen) {
                var submenu = document.getElementById('tasks-submenu');
                if (submenu) {
                    submenu.classList.add('open');
                    var ch = submenu.previousElementSibling.querySelector('.chevron');
                    if (ch) ch.classList.add('open');
                }
            }
            if (state.statuses) {
                state.statuses.forEach(function(name) {
                    var headers = document.querySelectorAll('.nav-sub-header');
                    for (var i = 0; i < headers.length; i++) {
                        var sp = headers[i].querySelector('span');
                        if (sp && sp.textContent === name) {
                            var p = headers[i].closest('.nav-sub-status');
                            if (p) p.classList.add('open');
                            var s = headers[i].nextElementSibling;
                            if (s) s.classList.add('open');
                            var c = headers[i].querySelector('.chevron');
                            if (c) c.classList.add('open');
                            break;
                        }
                    }
                });
            }
            if (state.scrollTop) {
                var sb = document.querySelector('.sidebar');
                if (sb) sb.scrollTop = state.scrollTop;
            }
            window.__sidebarRestored = true;
        })();
        </script>
        <main class="main-content">
            <div class="page-header">
                <div class="page-header-left">
                    <h1>Bienvenido {{ $displayName }}</h1>
                    <h2 class="page-subtitle">
                        @if ($hasTasks)
                            Tienes {{ $stats['pending'] }} tareas pendientes.
                        @else
                            Comienza creando tu primera tarea.
                        @endif
                    </h2>
                </div>
            </div>

            <div class="charts-stack">
                <div class="chart-card" id="card-chart-donut">
                    <div class="chart-card-header">
                        <h3>Distribución de Tareas</h3>
                    </div>
                    <div class="chart-body">
                        <div class="chart-skeleton">
                            <div class="skeleton-donut skeleton-pulse"></div>
                        </div>
                        @if ($hasTasks)
                        <div id="chart-donut" class="chart-container chart-data"></div>
                        @else
                        <div class="chart-empty">
                            <div class="chart-empty-bg">
                                <svg class="empty-ring" viewBox="0 0 120 120">
                                    <circle cx="60" cy="60" r="48" fill="none" stroke="currentColor" stroke-width="2" stroke-dasharray="4 4" opacity="0.15"/>
                                    <circle cx="60" cy="60" r="48" fill="none" stroke="currentColor" stroke-width="6" stroke-dasharray="100 150" stroke-dashoffset="-30" opacity="0.06"/>
                                </svg>
                            </div>
                            <div class="chart-empty-content">
                                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                                    <path d="M9 14l2 2 4-4"/>
                                </svg>
                                <p class="empty-title">Sin tareas aún</p>
                                <p class="empty-desc">Crea tu primera tarea para ver estadísticas aquí.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="chart-card" id="card-chart-weekly">
                    <div class="chart-card-header">
                        <h3>Tareas por Día de la Semana</h3>
                    </div>
                    <div class="chart-body">
                        <div class="chart-skeleton">
                            <div class="skeleton-bar-group skeleton-pulse">
                                <div class="skeleton-bar" style="height:60px"></div>
                                <div class="skeleton-bar" style="height:90px"></div>
                                <div class="skeleton-bar" style="height:40px"></div>
                                <div class="skeleton-bar" style="height:110px"></div>
                                <div class="skeleton-bar" style="height:70px"></div>
                                <div class="skeleton-bar" style="height:50px"></div>
                                <div class="skeleton-bar" style="height:30px"></div>
                            </div>
                        </div>
                        @if ($hasTasks)
                        <div id="chart-weekly" class="chart-container chart-data"></div>
                        @else
                        <div class="chart-empty">
                            <div class="chart-empty-bg">
                                <svg class="empty-grid" viewBox="0 0 200 140">
                                    <line x1="20" y1="0" x2="20" y2="140" opacity="0.08"/>
                                    <line x1="50" y1="0" x2="50" y2="140" opacity="0.08"/>
                                    <line x1="80" y1="0" x2="80" y2="140" opacity="0.08"/>
                                    <line x1="110" y1="0" x2="110" y2="140" opacity="0.08"/>
                                    <line x1="140" y1="0" x2="140" y2="140" opacity="0.08"/>
                                    <line x1="170" y1="0" x2="170" y2="140" opacity="0.08"/>
                                    <line x1="0" y1="20" x2="200" y2="20" opacity="0.06"/>
                                    <line x1="0" y1="50" x2="200" y2="50" opacity="0.06"/>
                                    <line x1="0" y1="80" x2="200" y2="80" opacity="0.06"/>
                                    <line x1="0" y1="110" x2="200" y2="110" opacity="0.06"/>
                                </svg>
                                <div class="empty-bars">
                                    <div style="height:40px"></div>
                                    <div style="height:70px"></div>
                                    <div style="height:30px"></div>
                                    <div style="height:90px"></div>
                                    <div style="height:55px"></div>
                                    <div style="height:35px"></div>
                                    <div style="height:20px"></div>
                                </div>
                            </div>
                            <div class="chart-empty-content">
                                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                                <p class="empty-title">Sin actividad semanal</p>
                                <p class="empty-desc">Las tareas aparecerán aquí distribuidas por día.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="chart-card" id="card-chart-monthly">
                    <div class="chart-card-header">
                        <h3>Tareas por Mes</h3>
                    </div>
                    <div class="chart-body">
                        <div class="chart-skeleton">
                            <div class="skeleton-bar-group skeleton-pulse" style="flex-direction:column;align-items:stretch;gap:8px;height:200px;padding:20px">
                                <div class="skeleton-bar" style="width:70%;height:16px;border-radius:6px"></div>
                                <div class="skeleton-bar" style="width:45%;height:16px;border-radius:6px"></div>
                                <div class="skeleton-bar" style="width:85%;height:16px;border-radius:6px"></div>
                                <div class="skeleton-bar" style="width:30%;height:16px;border-radius:6px"></div>
                            </div>
                        </div>
                        @if ($hasTasks)
                        <div id="chart-monthly" class="chart-container chart-data"></div>
                        @else
                        <div class="chart-empty">
                            <div class="chart-empty-bg">
                                <svg class="empty-grid" viewBox="0 0 200 140">
                                    <line x1="0" y1="20" x2="200" y2="20" opacity="0.06"/>
                                    <line x1="0" y1="50" x2="200" y2="50" opacity="0.06"/>
                                    <line x1="0" y1="80" x2="200" y2="80" opacity="0.06"/>
                                    <line x1="0" y1="110" x2="200" y2="110" opacity="0.06"/>
                                    <line x1="0" y1="0" x2="0" y2="140" opacity="0.08"/>
                                    <line x1="50" y1="0" x2="50" y2="140" opacity="0.08"/>
                                    <line x1="100" y1="0" x2="100" y2="140" opacity="0.08"/>
                                    <line x1="150" y1="0" x2="150" y2="140" opacity="0.08"/>
                                </svg>
                                <div class="empty-bars" style="flex-direction:column;align-items:flex-start;height:auto;gap:10px;width:70%">
                                    <div style="width:75%;height:14px;border-radius:4px"></div>
                                    <div style="width:50%;height:14px;border-radius:4px"></div>
                                    <div style="width:88%;height:14px;border-radius:4px"></div>
                                </div>
                            </div>
                            <div class="chart-empty-content">
                                <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <line x1="12" y1="20" x2="12" y2="10"/>
                                    <line x1="18" y1="20" x2="18" y2="4"/>
                                    <line x1="6" y1="20" x2="6" y2="16"/>
                                </svg>
                                <p class="empty-title">Sin tendencia mensual</p>
                                <p class="empty-desc">Los datos mensuales se mostrarán cuando tengas tareas.</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>


        </main>
    </div>
    @include('partials.create-task-modal')
    @include('partials.logout-modal')


@if (session('success'))
<script>document.addEventListener('DOMContentLoaded', function(){ showToast('{{ session('success') }}', 'success'); });</script>
@endif
@if (session('error'))
<script>document.addEventListener('DOMContentLoaded', function(){ showToast('{{ session('error') }}', 'error'); });</script>
@endif
<div id="toast-container"></div>
</body>
</html>
