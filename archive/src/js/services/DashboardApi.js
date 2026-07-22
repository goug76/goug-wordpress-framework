/**
 * DashboardApi
 *
 * Centralized communication layer for all
 * authenticated dashboard AJAX requests.
 *
 * This class abstracts WordPress AJAX details
 * away from dashboard modules so features only
 * need to specify the action and payload.
 */
export default class DashboardApi {

	/**
	 * Send a POST request to a dashboard AJAX action.
	 *
	 * @param {string} action AJAX action.
	 * @param {Object} data Request payload.
	 *
	 * @returns {Promise<Response|null>}
	 */
	static async post(action, data = {}) {

		if (!window.ajaxurl) {
			return null;
		}

		const formData = new FormData();

		formData.append(
			'action',
			action
		);

		Object.entries(data).forEach(
			([key, value]) => {

				formData.append(
					key,
					value
				);

			}
		);

		formData.append(
			'_ajax_nonce',
			gougDashboard.nonce
		);

		try {

			const response = await fetch(
				window.ajaxurl,
				{
					method: 'POST',
					credentials: 'same-origin',
					body: formData,
				}
			);

			return response;

		} catch (error) {

			console.error(error);

			return null;

		}

	}

}