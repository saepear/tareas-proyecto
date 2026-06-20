@php
$filterStatus = request('status');

$stats = [
    'total' => $tasks->count(),
    'pending' => $tasks->filter(fn($t) => $t->status->name === 'pending')->count(),
    'in_progress' => $tasks->filter(fn($t) => $t->status->name === 'in-progress')->count(),
    'completed' => $tasks->filter(fn($t) => $t->status->name === 'completed')->count(),
];

$pageInfo = [
    '' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
    'all' => ['title' => 'Todas las Tareas', 'subtitle' => 'Tienes ' . $stats['total'] . ' tareas en total.'],
    'pending' => ['title' => 'Tareas Pendientes', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas pendientes.'],
    'in-progress' => ['title' => 'Tareas en Progreso', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas en progreso.'],
    'completed' => ['title' => 'Tareas Completadas', 'subtitle' => 'Tienes ' . $filteredTasks->count() . ' tareas completadas.'],
];

$currentPage = $pageInfo[$filterStatus] ?? $pageInfo['all'];

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
    <title>Ideas En Movimiento — Task Manager</title>
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
    @vite('resources/css/app.css')
    <script src="{{ asset('js/theme.js') }}"></script>
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
                    <h1>Ideas En Movimiento</h1>
                    <h2 class="page-subtitle">{{ $currentPage['subtitle'] }}</h2>
                </div>
            </div>

            <div class="tasks-container" id="tasks-container">
                @include('partials.tasks-container')
            </div>
        </main>
    </div>

    @include('partials.create-task-modal')
    @include('partials.logout-modal')

    {{-- Edit & Delete Modals --}}
    @foreach ($tasks as $task)
    <dialog id="modal-edit-task-{{ $task->id }}" class="neu-modal">
        <div class="neu-modal-content">
            <div class="neu-modal-header">
                <h2>Editar Tarea</h2>
                <button class="neu-modal-close" onclick="prettyModal.close('modal-edit-task-{{ $task->id }}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
            <form action="{{ route('tasks.update', $task) }}" method="POST" class="modal-form">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="edit-title-{{ $task->id }}">Título</label>
                    <input type="text" id="edit-title-{{ $task->id }}" name="title" value="{{ $task->title }}" required>
                </div>
                <div class="form-group">
                    <label for="edit-description-{{ $task->id }}">Descripción</label>
                    <textarea id="edit-description-{{ $task->id }}" name="description">{{ $task->description }}</textarea>
                </div>
                <div class="form-group">
                    <label>Estado</label>
                    @if ($task->status && $task->status->name === 'completed')
                    <div class="completed-badge">Estado actual: <span>Completada</span></div>
                    @endif
                    <select id="edit-status_id-{{ $task->id }}" name="status_id" required>
                        @foreach ($editableStates as $state)
                        <option value="{{ $state->id }}" {{ $task->status_id === $state->id ? 'selected' : '' }}>
                            {{ $statusNameToLabel[$state->name] ?? $state->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-priority_id-{{ $task->id }}">Prioridad</label>
                    <select id="edit-priority_id-{{ $task->id }}" name="priority_id" required>
                        @foreach ($priorityTypes as $priority)
                        <option value="{{ $priority->id }}" {{ $task->priority_id === $priority->id ? 'selected' : '' }}>
                            {{ ucfirst($priority->name) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="edit-due_date-{{ $task->id }}">Fecha Límite</label>
                    <input type="date" id="edit-due_date-{{ $task->id }}" name="due_date" value="{{ $task->due_date->format('Y-m-d') }}" required>
                </div>
            </form>
            <div class="neu-modal-footer">
                <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-edit-task-{{ $task->id }}')">Cancelar</button>
                <button class="btn-neu btn-neu-sm btn-neu-primary" onclick="var f=this.closest('.neu-modal-content').querySelector('form');if(!f.checkValidity()){if(!f.title.value)showToast('No puedes crear una tarea sin título','error');if(!f.due_date.value)showToast('La fecha no puede estar vacía','error')}else f.submit()">Guardar Cambios</button>
            </div>
        </div>
    </dialog>

    <dialog id="modal-delete-task-{{ $task->id }}" class="neu-modal">
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
            <div class="delete-task-title">"{{ $task->title }}"</div>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST">
                @csrf
                @method('DELETE')
                <div class="neu-modal-footer" style="justify-content:center;">
                    <button class="btn-neu btn-neu-sm" type="button" onclick="prettyModal.close('modal-delete-task-{{ $task->id }}')">Cancelar</button>
                    <button class="btn-neu btn-neu-sm btn-neu-danger" type="submit">Eliminar</button>
                </div>
            </form>
        </div>
    </dialog>
    @endforeach

@if (session('success'))
<script>document.addEventListener('DOMContentLoaded', function(){ showToast('{{ session('success') }}', 'success'); });</script>
@endif
@if (session('error'))
<script>document.addEventListener('DOMContentLoaded', function(){ showToast('{{ session('error') }}', 'error'); });</script>
@endif
<div id="toast-container"></div>
</body>
</html>
