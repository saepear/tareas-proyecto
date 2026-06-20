class PrettyModal {
    constructor() {
        this.injectStyles();
        this.initDraftListeners();
    }

    initDraftListeners() {
        document.addEventListener('submit', function (e) {
            var form = e.target;
            var dialog = form.closest('.neu-modal');
            if (dialog && dialog.id) {
                this.clearDraft(dialog.id);
            }
        }.bind(this));

        document.addEventListener('close', function (e) {
            var dialog = e.target;
            if (dialog && dialog.id && dialog.classList.contains('neu-modal')) {
                this.saveDraft(dialog.id);
            }
        }.bind(this), true);
    }

    setupBackdropHandler(dialog) {
        if (dialog.dataset.backdropHandler) return;
        dialog.dataset.backdropHandler = '1';

        dialog.addEventListener('cancel', function (e) {
            e.preventDefault();
            this.close(dialog.id);
        }.bind(this));

        dialog.addEventListener('mousedown', function (e) {
            if (e.target === dialog) {
                e.preventDefault();
                e.stopPropagation();
                this.close(dialog.id);
            }
        }.bind(this));
    }

    open(dialogId, event) {
        const dialog = document.getElementById(dialogId);
        if (!dialog) return;

        const origin = event ? event.currentTarget : null;
        const randomId = Math.random().toString(16).slice(2);

        if (origin) {
            dialog.dataset.flipId = randomId;
            origin.dataset.flipId = randomId;
        }

        const originState = origin ? Flip.getState(origin) : null;

        dialog.showModal();
        this.setupBackdropHandler(dialog);
        this.restoreDraft(dialogId);

        if (originState) {
            Flip.from(originState, {
                targets: dialog,
                scale: true,
                ease: CustomEase.create("custom", "M0,0 C0.305,0.206 0.116,0.567 0.3,0.8 0.394,0.921 0.491,1 1,1"),
                toggleClass: 'pretty-modal-opening',
                duration: 0.5,
                onComplete: () => {
                    dialog.removeAttribute('style');
                },
            });
        }
    }

    close(dialogId) {
        const dialog = document.getElementById(dialogId);
        if (!dialog) return;

        this.saveDraft(dialogId);

        const originId = dialog.dataset.flipId;
        const origin = document.querySelector(`[data-flip-id="${originId}"]:not([open])`);

        const originState = Flip.getState(origin);

        Flip.to(originState, {
            targets: dialog,
            scale: true,
            ease: CustomEase.create("custom", "M0,0 C0.305,0.206 0.116,0.567 0.3,0.8 0.394,0.921 0.491,1 1,1"),
            onComplete: () => {
                dialog.setAttribute("style", "");
                dialog.close();
            },
            toggleClass: 'pretty-modal-closing',
            duration: 0.35,
        });
    }

    saveDraft(dialogId) {
        var form = document.querySelector('#' + dialogId + ' form');
        if (!form) return;
        var data = {};
        form.querySelectorAll('input, textarea, select').forEach(function (field) {
            if (field.name) data[field.name] = field.value;
        });
        try {
            sessionStorage.setItem('draft_' + dialogId, JSON.stringify(data));
        } catch (e) {}
    }

    restoreDraft(dialogId) {
        var raw;
        try {
            raw = sessionStorage.getItem('draft_' + dialogId);
        } catch (e) { return; }
        if (!raw) return;
        var data;
        try { data = JSON.parse(raw); } catch (e) { return; }
        var form = document.querySelector('#' + dialogId + ' form');
        if (!form) return;
        Object.keys(data).forEach(function (name) {
            var field = form.querySelector('[name="' + name + '"]');
            if (field) field.value = data[name];
        });
    }

    clearDraft(dialogId) {
        try {
            sessionStorage.removeItem('draft_' + dialogId);
        } catch (e) {}
    }

    injectStyles() {
        if (document.getElementById('pretty-modal-styles')) return;

        const styles = `

            .pretty-modal-opening {
                animation: pretty-modal-opening 300ms cubic-bezier(.56,.27,0,1);
            }

            @keyframes pretty-modal-opening{
                from { opacity: 0; filter: blur(8px) } to { opacity: 1; filter: blur(0px) }
            }

            .pretty-modal-closing {
                animation: 
                    pretty-modal-closing-border-radius 300ms cubic-bezier(.56,.27,0,1), 
                    pretty-modal-closing-blur 300ms cubic-bezier(.37,.35,0,1), 
                    pretty-modal-closing-fade 350ms cubic-bezier(.56,.27,0,1)
                ;
            }
            
            @keyframes pretty-modal-closing-border-radius {
                to { border-radius:400px; }
            }

            @keyframes pretty-modal-closing-blur {
                0% { filter: blur(0); } 100% { filter: blur(20px); }
            }

            @keyframes pretty-modal-closing-fade {
                from { opacity: 1; } to { opacity: 0; }
            }

        `;

        const styleSheet = document.createElement('style');
        styleSheet.id = 'pretty-modal-styles';
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    }
}

globalThis.prettyModal = new PrettyModal();
