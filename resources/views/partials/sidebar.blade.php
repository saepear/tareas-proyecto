<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width:36px;height:36px;object-fit:contain;border-radius:50%;">
        <span>TaskManager</span>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item nav-item-dashboard {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <a href="#" class="nav-item {{ request()->routeIs('tasks') ? 'active' : '' }}" onclick="toggleTasksSubmenu(); return false;">
            <svg viewBox="0 0 512 512" fill="currentColor">
                <path d="M152.1 38.2c9.9 8.9 10.7 24 1.8 33.9l-72 80c-4.4 4.9-10.6 7.8-17.2 7.9s-12.9-2.4-17.6-7L7 113c-9.3-9.4-9.3-24.6 0-34s24.6-9.4 33.9 0L63 101.1l55.1-61.2c8.9-9.9 24-10.7 33.9-1.8zm0 160c9.9 8.9 10.7 24 1.8 33.9l-72 80c-4.4 4.9-10.6 7.8-17.2 7.9s-12.9-2.4-17.6-7L7 273c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0L63 261.2l55.1-61.2c8.9-9.9 24-10.7 33.9-1.8zM224 96c0-17.7 14.3-32 32-32h224c17.7 0 32 14.3 32 32s-14.3 32-32 32H256c-17.7 0-32-14.3-32-32m0 160c0-17.7 14.3-32 32-32h224c17.7 0 32 14.3 32 32s-14.3 32-32 32H256c-17.7 0-32-14.3-32-32m-64 160c0-17.7 14.3-32 32-32h288c17.7 0 32 14.3 32 32s-14.3 32-32 32H192c-17.7 0-32-14.3-32-32M48 368a48 48 0 1 1 0 96a48 48 0 1 1 0-96"/>
            </svg>
            <span>Tareas</span>
            <svg class="chevron {{ request()->routeIs('tasks') ? 'open' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </a>
        <div class="nav-submenu {{ request()->routeIs('tasks') ? 'open' : '' }}" id="tasks-submenu">
            <a href="{{ route('tasks') }}" class="nav-sub-item {{ request()->routeIs('tasks') && (!request('status') || request('status') === 'all') && !request('priority') ? 'active' : '' }}">
                <span>Todas</span>
                <span class="nav-badge" data-badge="total">{{ $stats['total'] }}</span>
            </a>

            @php
            $statuses = [
                'pending' => 'Pendientes',
                'in-progress' => 'En Progreso',
                'completed' => 'Completadas',
            ];
            @endphp

            @foreach ($statuses as $statusKey => $statusLabel)
            @php $isOpen = request('status') === $statusKey; @endphp
            <div class="nav-sub-status {{ $isOpen ? 'open' : '' }}">
                <a href="#" class="nav-sub-item nav-sub-header" onclick="togglePrioritySubmenu(this); return false;">
                    <span>{{ $statusLabel }}</span>
                    <span class="nav-badge" data-badge="status" data-status="{{ $statusKey }}">{{ $stats[str_replace('-', '_', $statusKey)] ?? 0 }}</span>
                    <svg class="chevron {{ $isOpen ? 'open' : '' }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </a>
                <div class="nav-priority-submenu {{ $isOpen ? 'open' : '' }}">
                    @foreach (['alta', 'media', 'baja'] as $priority)
                    @php
                    $count = $combinedStats[$statusKey][$priority] ?? 0;
                    $isActive = request('status') === $statusKey && request('priority') === $priority;
                    @endphp
                    <a href="{{ route('tasks', ['status' => $statusKey, 'priority' => $priority]) }}" class="nav-sub-item nav-sub-priority {{ $isActive ? 'active' : '' }}" data-filter="true">
                        <span>{{ ucfirst($priority) }}</span>
                        <span class="nav-badge" data-badge="priority" data-status="{{ $statusKey }}" data-priority="{{ $priority }}">{{ $count }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <a href="#" class="nav-item" style="margin-top:12px" onclick="prettyModal.open('modal-create-task', event); return false;">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="12" y1="5" x2="12" y2="19"/>
                <line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            <span>Nueva Tarea</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
            </div>
            <div class="user-details">
                <div class="user-name">{{ $displayName }}</div>
                <div class="user-email">{{ $user->email ?? '' }}</div>
            </div>
            <button class="settings-btn" onclick="prettyModal.open('modal-settings', event)" title="Settings">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="3"/>
                    <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                </svg>
            </button>
            <button class="logout-btn" onclick="prettyModal.open('modal-logout', event)" title="Cerrar sesión">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                    <polyline points="16 17 21 12 16 7"/>
                    <line x1="21" y1="12" x2="9" y2="12"/>
                </svg>
            </button>
        </div>
    </div>
</aside>

<dialog id="modal-settings" class="neu-modal">
    <div class="neu-modal-content">
        <div class="neu-modal-header">
            <h2>Settings</h2>
            <button class="neu-modal-close" onclick="prettyModal.close('modal-settings')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">Profile</div>
            <div class="avatar-upload">
                <div class="avatar-preview" onclick="document.getElementById('avatar-input').click()">
                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                </div>
                <div>
                    <div class="avatar-upload-btn" onclick="document.getElementById('avatar-input').click()">
                        Upload Photo
                    </div>
                    <input type="file" id="avatar-input" accept="image/*" onchange="previewAvatar(this)" style="display:none">
                    <p style="font-size:12px;color:var(--text-muted);margin-top:8px;">Optional. JPG, PNG or GIF.</p>
                </div>
            </div>
            <div class="avatar-controls" id="avatar-controls" style="display:none">
                <div class="control-group">
                    <label>Zoom</label>
                    <input type="range" class="avatar-range" id="avatar-zoom" min="100" max="300" value="100" step="5">
                    <span class="range-value" id="zoom-value">100%</span>
                </div>
                <div class="control-row">
                    <div class="control-group">
                        <label>Horizontal</label>
                        <input type="range" class="avatar-range" id="avatar-x" min="-30" max="30" value="0">
                    </div>
                    <div class="control-group">
                        <label>Vertical</label>
                        <input type="range" class="avatar-range" id="avatar-y" min="-30" max="30" value="0">
                    </div>
                </div>
            </div>
            <div class="form-group" style="margin-bottom:0">
                <label for="settings-display-name">Display Name</label>
                <input type="text" id="settings-display-name" placeholder="How others see you" maxlength="255">
            </div>
        </div>

        <div class="settings-section">
            <div class="settings-section-title">Appearance</div>
            <div class="theme-toggle-group">
                <button class="theme-option" data-mode="light" onclick="toggleTheme('light')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="5"/>
                        <line x1="12" y1="1" x2="12" y2="3"/>
                        <line x1="12" y1="21" x2="12" y2="23"/>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                        <line x1="1" y1="12" x2="3" y2="12"/>
                        <line x1="21" y1="12" x2="23" y2="12"/>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                    </svg>
                    Light
                </button>
                <button class="theme-option" data-mode="dark" onclick="toggleTheme('dark')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                    Dark
                </button>
            </div>
        </div>

        <div class="neu-modal-footer">
            <button class="btn-neu btn-neu-sm" onclick="prettyModal.close('modal-settings')">Cancel</button>
            <button class="btn-neu btn-neu-sm btn-neu-primary" onclick="saveSettings()">Save Changes</button>
        </div>
    </div>
</dialog>
