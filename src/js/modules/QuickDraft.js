export default class QuickDraft {
	constructor() {
		this.form = document.querySelector(
			'[data-quick-draft-form]'
		);

		if (!this.form) {
			return;
		}

		this.content = this.form.querySelector(
			'[data-quick-draft-content]'
		);

		this.counter = this.form.querySelector(
			'[data-quick-draft-counter]'
		);

		this.notice = this.form.querySelector(
			'[data-quick-draft-notice]'
		);

		this.submitButton = this.form.querySelector(
			'button[type="submit"]'
		);

		this.buttonText = this.form.querySelector(
			'[data-quick-draft-button-text]'
		);

		this.defaultButtonText =
			this.buttonText?.textContent.trim() || 'Save Draft';

		this.events();
		this.updateCounter();
	}

	/**
	 * Register module event listeners.
	 */
	events() {
		this.content?.addEventListener(
			'input',
			this.updateCounter.bind(this)
		);

		this.form.addEventListener(
			'submit',
			this.submitForm.bind(this)
		);
	}

	/**
	 * Update the content character counter.
	 */
	updateCounter() {
		if (!this.content || !this.counter) {
			return;
		}

		const maximumLength =
			this.content.maxLength > 0
				? this.content.maxLength
				: 1000;

		this.counter.textContent =
			`${this.content.value.length} / ${maximumLength}`;
	}

	/**
	 * Submit the Quick Draft form through WordPress AJAX.
	 *
	 * @param {SubmitEvent} event Form submission event.
	 */
	async submitForm(event) {
		event.preventDefault();

		this.setLoadingState(true);
		this.hideNotice();

		try {
			const result = await this.sendRequest();

			if (!result.success) {
				throw new Error(
					result?.data?.message ||
					'Unable to save the draft.'
				);
			}

			this.showNotice(
				result.data.message,
				'success'
			);

			this.resetForm();
		} catch (error) {
			this.showNotice(
				error instanceof Error
					? error.message
					: 'Unable to save the draft.',
				'error'
			);
		} finally {
			this.setLoadingState(false);
		}
	}

	/**
	 * Send the AJAX request.
	 *
	 * @returns {Promise<object>} WordPress JSON response.
	 */
	async sendRequest() {
		if (!window.ajaxurl) {
			throw new Error(
				'The WordPress AJAX URL is unavailable.'
			);
		}

		const response = await fetch(window.ajaxurl, {
			method: 'POST',
			credentials: 'same-origin',
			body: new FormData(this.form),
		});

		let result;

		try {
			result = await response.json();
		} catch {
			throw new Error(
				'WordPress returned an invalid response.'
			);
		}

		if (!response.ok) {
			throw new Error(
				result?.data?.message ||
				'Unable to save the draft.'
			);
		}

		return result;
	}

	/**
	 * Toggle the form loading state.
	 *
	 * @param {boolean} isLoading Whether the form is loading.
	 */
	setLoadingState(isLoading) {
		if (this.submitButton) {
			this.submitButton.disabled = isLoading;
		}

		if (this.buttonText) {
			this.buttonText.textContent = isLoading
				? 'Saving...'
				: this.defaultButtonText;
		}

		this.form.setAttribute(
			'aria-busy',
			isLoading ? 'true' : 'false'
		);
	}

	/**
	 * Display a form status message.
	 *
	 * @param {string} message Message to display.
	 * @param {string} state   Notice state.
	 */
	showNotice(message, state) {
		if (!this.notice) {
			return;
		}

		this.notice.textContent = message;
		this.notice.dataset.state = state;
		this.notice.hidden = false;
	}

	/**
	 * Hide the current form notice.
	 */
	hideNotice() {
		if (!this.notice) {
			return;
		}

		this.notice.hidden = true;
		this.notice.textContent = '';
		delete this.notice.dataset.state;
	}

	/**
	 * Reset the form after a successful save.
	 */
	resetForm() {
		this.form.reset();
		this.updateCounter();

		const titleField = this.form.querySelector(
			'input[name="title"]'
		);

		titleField?.focus();
	}
}