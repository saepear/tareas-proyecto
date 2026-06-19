<dialog id="modal-logout" class="neu-modal">
    <div class="neu-modal-content" style="text-align:center;">
        <div class="logout-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
        </div>
        <div class="neu-modal-header" style="justify-content:center;margin-bottom:16px;">
            <h2>Cerrar sesión</h2>
        </div>
        <p class="logout-text">¿Estás seguro de que deseas cerrar sesión?</p>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <div class="neu-modal-footer" style="justify-content:center;">
                <button class="btn-neu btn-neu-sm" type="button" onclick="prettyModal.close('modal-logout')">Cancelar</button>
                <button class="btn-neu btn-neu-sm btn-neu-danger" type="submit">Sí, cerrar sesión</button>
            </div>
        </form>
    </div>
</dialog>
