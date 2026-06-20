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

        <div class="nav-item-group">
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
                <a href="{{ route('tasks') }}" class="nav-sub-item {{ request()->routeIs('tasks') && (!request('status') || request('status') === 'all') && !request('priority') ? 'active' : '' }}" data-filter="true">
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
        </div>

        <a href="#" class="nav-item" onclick="prettyModal.open('modal-create-task', event); return false;">
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
            <button class="settings-btn" id="open-settings-btn" onclick="toggleSettingsModal()" title="Settings">
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

<!-- Avatar Crop Modal (outside sidebar to avoid transform breaking position:fixed) -->
<div id="avatar-pretty-modal" class="avatar-pretty-modal">
    <div class="pretty-backdrop"></div>
    <div class="pretty-panel">
        <div class="pretty-panel-header">
            <span>Ajustar foto de perfil</span>
            <button class="pretty-close-btn" onclick="closeCropModal()">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="pretty-crop-area">
            <img id="crop-image" src="">
        </div>
        <div class="pretty-panel-footer">
            <button class="btn-neu btn-neu-sm" onclick="closeCropModal()">Cancelar</button>
            <button class="btn-neu btn-neu-sm btn-neu-primary" onclick="applyCrop()">Aplicar</button>
        </div>
    </div>
</div>

<!-- Settings Modal -->
<div id="settings-pretty-modal" class="settings-modal-overlay" style="display: none;">
    <div id="settings-backdrop" class="settings-modal-backdrop"></div>

    <div id="settings-card" class="settings-modal-card settings-modal-card--wide">
        <div class="settings-modal-header">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="settings-modal-header-icon">
                <circle cx="12" cy="12" r="3"/>
                <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
            </svg>
            <span>Settings</span>
        </div>

        <div class="settings-modal-body settings-modal-body--split">
            <!-- Left Navigation -->
            <nav class="settings-nav" id="settings-nav">
                <button class="settings-nav-btn active" data-tab="mi-cuenta">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Mi Cuenta
                </button>
                <button class="settings-nav-btn" data-tab="apariencia">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="3"/>
                        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/>
                    </svg>
                    Apariencia
                </button>
                <button class="settings-nav-btn" data-tab="seguridad">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Seguridad
                </button>
            </nav>

            <!-- Right Content Panel -->
            <div class="settings-content" id="settings-content">
                <!-- Mi Cuenta -->
                <div class="settings-tab-panel" data-panel="mi-cuenta">
                    <div class="settings-group">
                        <label class="settings-label">Display Name</label>
                        <input type="text" id="settings-display-name" class="settings-input" placeholder="How others see you" maxlength="255">
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Avatar</label>
                        <div class="settings-avatar-wrap">
                            <div class="settings-avatar" id="settings-avatar-trigger">
                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                                <div class="avatar-hover-overlay">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                        <circle cx="12" cy="13" r="4"/>
                                    </svg>
                                    <span class="avatar-hover-text">Ajustar Foto</span>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="avatar-input" accept="image/*" style="display:none">
                    </div>
                </div>

                <!-- Apariencia -->
                <div class="settings-tab-panel" data-panel="apariencia">
                    <div>
                        <div class="settings-section-title">Paleta de Colores</div>
                        <div class="color-palette-grid">
                            <button class="color-swatch" data-palette="1" style="background:var(--palette-1)"></button>
                            <button class="color-swatch" data-palette="2" style="background:var(--palette-2)"></button>
                            <button class="color-swatch" data-palette="3" style="background:var(--palette-3)"></button>
                            <button class="color-swatch" data-palette="4" style="background:var(--palette-4)"></button>
                            <button class="color-swatch" data-palette="5" style="background:var(--palette-5)"></button>
                            <button class="color-swatch" data-palette="6" style="background:var(--palette-6)"></button>
                            <button class="color-swatch" data-palette="7" style="background:var(--palette-7)"></button>
                            <button class="color-swatch" data-palette="8" style="background:var(--palette-8)"></button>
                            <button class="color-swatch" data-palette="9" style="background:var(--palette-9)"></button>
                            <button class="color-swatch" data-palette="10" style="background:var(--palette-10)"></button>
                        </div>
                    </div>

                    <div>
                        <div class="settings-section-title">Estilos de Navegación</div>
                        <div class="nav-style-group">
                            <button class="nav-style-btn" data-nav-style="classic">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                                </svg>
                                Clásica
                            </button>
                            <button class="nav-style-btn" data-nav-style="pretty">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="5" width="18" height="4" rx="2"/><rect x="3" y="13" width="18" height="4" rx="2"/>
                                </svg>
                                Bonita
                            </button>
                            <button class="nav-style-btn" data-nav-style="dock">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="4" cy="12" r="2.5"/><circle cx="12" cy="12" r="2.5"/><circle cx="20" cy="12" r="2.5"/>
                                </svg>
                                Dock
                            </button>
                            <button class="nav-style-btn" data-nav-style="guapa">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                                Guapa
                            </button>
                        </div>
                    </div>

                    <div>
                        <div class="settings-section-title">Fuentes del Sistema</div>
                        <div class="font-list">
                            <button class="font-option-btn" data-font="moderna" style="font-family:var(--font-moderna)">
                                <span class="font-preview-label">Moderna</span>
                                <span class="font-preview-text">Inter — The quick brown fox jumps</span>
                            </button>
                            <button class="font-option-btn" data-font="tech" style="font-family:var(--font-tech)">
                                <span class="font-preview-label">Tech / Código</span>
                                <span class="font-preview-text">console.log('Hello World');</span>
                            </button>
                            <button class="font-option-btn" data-font="elegante" style="font-family:var(--font-elegante)">
                                <span class="font-preview-label">Elegante</span>
                                <span class="font-preview-text">Playfair — A fine selection indeed</span>
                            </button>
                            <button class="font-option-btn" data-font="clasica" style="font-family:var(--font-clasica)">
                                <span class="font-preview-label">Clásica</span>
                                <span class="font-preview-text">Georgia — The quick brown fox</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Seguridad -->
                <div class="settings-tab-panel" data-panel="seguridad">
                    <div class="settings-group">
                        <label class="settings-label">Tema del Sistema</label>
                        <p style="font-size:13px;color:var(--text-muted);line-height:1.5;">
                            El modo claro u oscuro se adapta según la configuración de tu sistema operativo.
                            Personaliza los colores de acento y sombras desde la pestaña <strong>Apariencia</strong>.
                        </p>
                    </div>
                    <div class="settings-group">
                        <label class="settings-label">Contraseña</label>
                        <p style="font-size:13px;color:var(--text-muted);line-height:1.5;">
                            Puedes cambiar tu contraseña desde la página de perfil.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-modal-footer">
            <button class="btn-neu btn-neu-sm" onclick="closeSettingsModal(true)">Cancelar</button>
            <button class="btn-neu btn-neu-sm btn-neu-primary" onclick="saveSettings()">Guardar</button>
        </div>
    </div>
</div>

