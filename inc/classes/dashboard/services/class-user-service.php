<?php
/**
 * Dashboard user data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides current-user information for the dashboard.
 */
class User_Service {

	/**
	 * Return current-user dashboard data.
	 *
	 * @return array
	 */
	public function get_data() {

		$current_user = wp_get_current_user();
		$current_hour = (int) wp_date( 'G' );

		if ( $current_hour < 12 ) {
			$greeting = __( 'Good morning', 'goug-framework' );
		} elseif ( $current_hour < 18 ) {
			$greeting = __( 'Good afternoon', 'goug-framework' );
		} else {
			$greeting = __( 'Good evening', 'goug-framework' );
		}

		return array(
			'id'           => (int) $current_user->ID,
			'display_name' => $current_user->display_name,
			'greeting'     => $greeting,
			'date'         => wp_date( 'l, F j, Y' ),
			'avatar_url'   => get_avatar_url(
				$current_user->ID,
				array(
					'size' => 96,
				)
			),
		);
	}
}