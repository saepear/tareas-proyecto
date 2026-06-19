@forelse ($filteredTasks as $task)
<div class="task-card" data-status="{{ $task->status->name }}" data-id="{{ $task->id }}" data-task-id="{{ $task->id }}">
    <div class="task-card-content">
        <div class="task-card-top">
            <div class="task-check">
                <input type="checkbox" id="task-{{ $task->id }}" {{ $task->status->name === 'completed' ? 'checked' : '' }}>
                <label for="task-{{ $task->id }}" class="task-check-label">
                    <span class="sr-only">Completar tarea: {{ $task->title }}</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </label>
            </div>
            <div class="task-card-title {{ $task->status->name === 'completed' ? 'completed' : '' }}">{{ $task->title }}</div>
        </div>
        @if ($task->description)
        <div class="task-card-desc">{{ $task->description }}</div>
        @endif
        <div class="task-card-meta">
            <span class="task-priority priority-{{ $task->priority->name }}">
                {{ ucfirst($task->priority->name) }}
            </span>
            <span class="task-status {{ $task->status->name }}">{{ $statusNameToLabel[$task->status->name] ?? $task->status->name }}</span>
            <span class="task-due {{ \Carbon\Carbon::parse($task->due_date)->isPast() && $task->status->name !== 'completed' ? 'overdue' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                {{ \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
            </span>
        </div>
    </div>
    <div class="task-card-actions">
        <button class="mui-btn" onclick="prettyModal.open('modal-edit-task-{{ $task->id }}', event)" title="Editar tarea">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Editar
        </button>
        <button class="mui-btn mui-btn-danger" onclick="prettyModal.open('modal-delete-task-{{ $task->id }}', event)" title="Eliminar tarea">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14">
                <polyline points="3 6 5 6 21 6"/>
                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
            </svg>
            Eliminar
        </button>
    </div>
</div>
@empty
<div class="empty-state">
    <div class="neu-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
            <polyline points="22 4 12 14.01 9 11.01"/>
        </svg>
    </div>
    <div class="state-content">
        <h3>No hay tareas aquí</h3>
        <p>No se encontraron tareas con este filtro.</p>
        <a href="{{ route('tasks') }}" class="btn-neu">Ver todas las tareas</a>
    </div>
</div>
@endforelse
