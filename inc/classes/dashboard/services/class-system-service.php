<?php
/**
 * Dashboard system data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides lightweight WordPress system information.
 */
class System_Service {

	/**
	 * Cached system data for the current request.
	 *
	 * @var array|null
	 */
	private $system_data = null;

	/**
	 * Return lightweight system information.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->system_data ) {
			return $this->system_data;
		}

		global $wp_version;

		$theme = wp_get_theme();

		$this->system_data = array(
			'wordpress_version' => (string) $wp_version,
			'php_version'       => PHP_VERSION,
			'theme_name'        => $theme->get( 'Name' ),
			'theme_version'     => $theme->get( 'Version' ),
			'is_https'          => is_ssl(),
			'debug_enabled'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'environment'       => function_exists( 'wp_get_environment_type' )
				? wp_get_environment_type()
				: 'production',
		);

		return $this->system_data;
	}
}