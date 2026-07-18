<?php
/**
 * Dashboard Settings Controller.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Controllers;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Services\User_Preferences_Service;

/**
 * Handles dashboard preference form submissions.
 */
class Dashboard_Settings_Controller {

	/**
	 * Dashboard preferences service.
	 *
	 * @var User_Preferences_Service
	 */
	private $preferences_service;

	/**
	 * Initialize the controller.
	 *
	 * @param User_Preferences_Service $preferences_service Preferences service.
	 */
	public function __construct(
		User_Preferences_Service $preferences_service
	) {

		$this->preferences_service = $preferences_service;
	}

	/**
	 * Register WordPress hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {

		add_action(
			'admin_post_goug_save_dashboard_preferences',
			array( $this, 'save_preferences' )
		);
	}

	/**
	 * Save the current user's dashboard preferences.
	 *
	 * @return void
	 */
	public function save_preferences() {

		if ( ! current_user_can( 'read' ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to update dashboard preferences.',
					'goug-framework'
				)
			);
		}

		check_admin_referer(
			'goug_save_dashboard_preferences',
			'goug_dashboard_preferences_nonce'
		);

		$density = isset( $_POST['density'] )
			? sanitize_key(
				wp_unslash( $_POST['density'] )
			)
			: 'comfortable';

		$show_greeting = isset(
			$_POST['show_greeting']
		);

		$enable_motion = isset(
			$_POST['enable_motion']
		);

		$updated = $this->preferences_service->update_preferences(
			array(
				'density'       => $density,
				'show_greeting' => $show_greeting,
				'enable_motion' => $enable_motion,
			)
		);

		$redirect_url = add_query_arg(
			array(
				'page'                   => 'goug-dashboard',
				'goug_preferences_saved' => $updated
					? '1'
					: '0',
			),
			admin_url( 'admin.php' )
		);

		wp_safe_redirect( $redirect_url );
		exit;
	}
}