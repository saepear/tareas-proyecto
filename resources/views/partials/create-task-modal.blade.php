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
        <form action="{{ route('tasks.store') }}" method="POST" class="modal-form">
            @csrf
            <div class="form-group">
                <label for="create-title">Título</label>
                <input type="text" id="create-title" name="title" placeholder="Nombre de la tarea" required>
            </div>
            <div class="form-group">
                <label for="create-description">Descripción</label>
                <textarea id="create-description" name="description" placeholder="Describe los detalles de la tarea..."></textarea>
            </div>
            <div class="form-group">
                <label for="create-status_id">Estado</label>
                <select id="create-status_id" name="status_id" required>
                    @foreach ($taskStates as $state)
                    <option value="{{ $state->id }}" {{ $state->name === 'pending' ? 'selected' : '' }}>
                        {{ $statusNameToLabel[$state->name] ?? $state->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="create-priority_id">Prioridad</label>
                <select id="create-priority_id" name="priority_id" required>
                    @foreach ($priorityTypes as $priority)
                    <option value="{{ $priority->id }}" {{ $priority->name === 'media' ? 'selected' : '' }}>
                        {{ ucfirst($priority->name) }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="create-due_date">Fecha Límite</label>
                <input type="date" id="create-due_date" name="due_date" required>
            </div>
        </form>
        <div class="neu-modal-footer">
            <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-create-task')">Cancelar</button>
            <button class="btn-neu btn-neu-sm btn-neu-primary" onclick="this.closest('.neu-modal-content').querySelector('form').submit()">Crear Tarea</button>
        </div>
    </div>
</dialog>
