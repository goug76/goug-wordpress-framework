export default class PanelToggle {
	constructor() {
		this.buttons = document.querySelectorAll(
			'.goug-panel__toggle'
		);

		if (!this.buttons.length) {
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
	}

	/**
	 * Handle a panel toggle click.
	 *
	 * @param {MouseEvent} event
	 */
	onToggleClick(event) {
		const button = event.currentTarget;

		const panel = button.closest('.goug-panel');

		if (!panel) {
			return;
		}

		this.toggle(panel, button);
	}

	/**
	 * Toggle a panel.
	 *
	 * @param {HTMLElement} panel
	 * @param {HTMLButtonElement} button
	 */
	toggle(panel, button) {
        const collapsed =
            panel.classList.toggle('is-collapsed');
        
        button.setAttribute(
            'aria-expanded',
            collapsed ? 'false' : 'true'
        );

        panel.dataset.collapsed =
	        collapsed ? 'true' : 'false';

        this.savePreference(
            panel,
            collapsed
        );
    }

    /**
     * Save a panel's collapsed state.
     *
     * @param {HTMLElement} panel
     * @param {boolean} collapsed
     */
    async savePreference(panel, collapsed) {

        if (!window.ajaxurl) {
            return;
        }

        const formData = new FormData();

        formData.append(
            'action',
            'goug_update_panel_state'
        );

        formData.append(
            'panel_id',
            panel.dataset.panelId
        );

        formData.append(
            'collapsed',
            collapsed ? '1' : '0'
        );

        formData.append(
            '_ajax_nonce',
            gougDashboard.nonce
        );

        try {
            await fetch(window.ajaxurl, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
            });
        } catch (error) {
            console.error(error);
        }
    }
}