import DashboardApi from '../services/DashboardApi';

export default class PanelToggle {
	constructor() {
		this.buttons = document.querySelectorAll(
			'.goug-panel__toggle'
		);

        this.headers = document.querySelectorAll(
            '.goug-panel__header'
        );

		if (!this.buttons.length && !this.headers.length) {
            return;
        }

		this.events();
	}

	/**
	 * Register module event listeners.
	 */
	events() {
        this.buttons.forEach((button) => {
            button.addEventListener(
                'click',
                this.onToggleClick.bind(this)
            );
        });

        this.headers.forEach((header) => {
            header.addEventListener(
                'click',
                this.onHeaderClick.bind(this)
            );
        });
    }

	/**
	 * Handle a panel toggle click.
	 *
	 * @param {MouseEvent} event
	 */
	onToggleClick(event) {
        event.stopPropagation();

		const button = event.currentTarget;

		const panel = button.closest('.goug-panel');

		if (!panel) {
			return;
		}

		if (panel.classList.contains('is-collapsed')) {

            this.expand(panel, button);

        } else {

            this.collapse(panel, button);

        }
	}

    /**
     * Handle a panel header click.
     *
     * @param {MouseEvent} event
     */
    onHeaderClick(event) {
        if ( event.target.closest( '.goug-drag-handle' ) ) {
            return;
        }
        const header = event.currentTarget;

        const panel = header.closest('.goug-panel');

        if (!panel) {
            return;
        }

        const button = panel.querySelector(
            '.goug-panel__toggle'
        );

        if (!button) {
            return;
        }

        if (panel.classList.contains('is-collapsed')) {

            this.expand(panel, button);

        } else {

            this.collapse(panel, button);

        }
    }

    /**
     * Collapse a panel.
     *
     * @param {HTMLElement} panel
     * @param {HTMLButtonElement} button
     */
    collapse(panel, button) {

        const body = panel.querySelector(
            '.goug-panel__body'
        );

        if (!body) {
            return;
        }

        const height = body.offsetHeight;

        body.style.height = `${height}px`;

        // Force a reflow so the browser acknowledges the fixed height.
        body.offsetHeight;

        panel.classList.add(
            'is-transitioning',
            'is-collapsing'
        );

        button.setAttribute(
            'aria-expanded',
            'false'
        );

        panel.dataset.collapsed = 'true';

        requestAnimationFrame(() => {
            body.style.height = '0px';
        });

        const onTransitionEnd = (event) => {

            if (
                event.target !== body ||
                event.propertyName !== 'height'
            ) {
                return;
            }

            body.removeEventListener(
                'transitionend',
                onTransitionEnd
            );

            body.style.height = '';

            panel.classList.remove(
                'is-transitioning',
                'is-collapsing'
            );

            panel.classList.add(
                'is-collapsed'
            );

            this.savePreference(
                panel,
                true
            );
        };

        body.addEventListener(
            'transitionend',
            onTransitionEnd
        );
    }

    /**
     * Expand a panel.
     *
     * @param {HTMLElement} panel
     * @param {HTMLButtonElement} button
     */
    expand(panel, button) {

        const body = panel.querySelector(
            '.goug-panel__body'
        );

        if (!body) {
            return;
        }

        panel.classList.remove(
            'is-collapsed'
        );

        panel.classList.add(
            'is-transitioning',
            'is-expanding'
        );

        // Start collapsed.
        body.style.height = '0px';

        // Measure the natural height.
        const height = body.scrollHeight;

        // Force layout.
        body.offsetHeight;

        button.setAttribute(
            'aria-expanded',
            'true'
        );

        panel.dataset.collapsed = 'false';

        requestAnimationFrame(() => {
            body.style.height = `${height}px`;
        });

        const onTransitionEnd = (event) => {

            if (
                event.target !== body ||
                event.propertyName !== 'height'
            ) {
                return;
            }

            body.removeEventListener(
                'transitionend',
                onTransitionEnd
            );

            body.style.height = '';

            panel.classList.remove(
                'is-transitioning',
                'is-expanding'
            );

            this.savePreference(
                panel,
                false
            );
        };

        body.addEventListener(
            'transitionend',
            onTransitionEnd
        );
    }

    /**
     * Save a panel's collapsed state.
     *
     * @param {HTMLElement} panel
     * @param {boolean} collapsed
     */
    async savePreference(panel, collapsed) {

        DashboardApi.post(
            'goug_update_panel_state',
            {
                panel_id: panel.dataset.panelId,
                collapsed: '1',
            }
        );

        DashboardApi.post(
            'goug_update_panel_state',
            {
                panel_id: panel.dataset.panelId,
                collapsed: '0',
            }
        );
    }
}