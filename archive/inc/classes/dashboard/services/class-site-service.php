<?php
/**
 * Dashboard site data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides basic WordPress site information.
 */
class Site_Service {

	/**
	 * Return basic site information.
	 *
	 * @return array
	 */
	public function get_data() {

		$site_name = get_bloginfo( 'name' );

		return array(
			'name'        => $site_name
				? $site_name
				: __( 'WordPress', 'goug-framework' ),
			'description' => get_bloginfo( 'description' ),
			'url'         => home_url( '/' ),
			'admin_url'   => admin_url(),
		);
	}
}