import Sortable from 'sortablejs';
/**
 * Dashboard panel sorting.
 */
class PanelSort {

	constructor() {

		this.grid = document.querySelector(
			'.goug-dashboard__grid'
		);

		if (!this.grid) {
			return;
		}

		this.handles = this.grid.querySelectorAll(
			'.goug-drag-handle'
		);

		this.events();
        this.initSortable();
	}

	/**
	 * Register panel sorting events.
	 *
	 * @return {void}
	 */
	events() {

		this.handles.forEach((handle) => {

			handle.addEventListener(
				'click',
				(event) => {
					event.stopPropagation();
				}
			);

			handle.addEventListener(
				'keydown',
				(event) => {
					this.onHandleKeydown(event);
				}
			);
		});
	}

    /**
     * Initialize pointer and touch panel sorting.
     *
     * @return {void}
     */
    initSortable() {

        this.sortable = new Sortable(
            this.grid,
            {
                draggable: '.goug-panel',
                handle: '.goug-drag-handle',

                animation: 200,

                swapThreshold: 0.65,
                invertSwap: true,
                invertedSwapThreshold: 0.65,

                forceFallback: true,
                fallbackOnBody: true,
                fallbackTolerance: 4,

                scroll: true,
                forceAutoScrollFallback: true,
                scrollSensitivity: 80,
                scrollSpeed: 12,
                bubbleScroll: true,

                ghostClass: 'goug-panel--ghost',
                chosenClass: 'goug-panel--chosen',
                dragClass: 'goug-panel--dragging',

                onEnd: () => {
                    this.logOrder();
                }
            }
        );
    }

    /**
     * Log the current panel order.
     *
     * Persistence will be added in a later PR.
     *
     * @return {void}
     */
    logOrder() {

        const order = Array.from(
            this.grid.querySelectorAll(
                '.goug-panel[data-panel-id]'
            )
        ).map((panel) => {
            return panel.dataset.panelId;
        });

        console.log(
            'Panel order:',
            order
        );
    }

	/**
	 * Return the current panel order.
	 *
	 * @returns {string[]}
	 */
	getOrder() {

		return Array.from(
			this.grid.querySelectorAll('.goug-panel')
		).map(
			panel => panel.dataset.panelId
		);

	}

	/**
	 * Save the current panel order.
	 */
	saveOrder() {

		DashboardApi.post(
			'goug_update_panel_order',
			{
				order: JSON.stringify(
					this.getOrder()
				),
			}
		);

	}

	/**
	 * Handle keyboard interaction with a panel sort handle.
	 *
	 * Keyboard reordering will be added in the next PR.
	 *
	 * @param {KeyboardEvent} event Keyboard event.
	 *
	 * @return {void}
	 */
	onHandleKeydown(event) {

		if (
			'ArrowUp' !== event.key &&
			'ArrowDown' !== event.key
		) {
			return;
		}

		event.preventDefault();
		event.stopPropagation();

		console.log(
			'Panel reorder:',
			event.key
		);
	}
}

export default PanelSort;