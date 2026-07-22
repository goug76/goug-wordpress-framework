<?php
/**
 * Dashboard Site Status panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Database_Service;
use GOUG\Inc\Dashboard\Services\System_Service;
use GOUG\Inc\Dashboard\Services\Update_Service;

/**
 * Registers and prepares the Site Status dashboard panel.
 *
 * Responsibilities:
 *
 * - Retrieve update, system, and database information.
 * - Compose service data into normalized status-card definitions.
 * - Register the Site Status panel and pass prepared data to its view.
 *
 * This panel coordinates existing services. It does not query
 * WordPress directly or render presentation markup.
 */
class Panel_Site_Status implements Dashboard_Panel {

	/**
	 * Update data service.
	 *
	 * @var Update_Service
	 */
	private $update_service;

	/**
	 * System data service.
	 *
	 * @var System_Service
	 */
	private $system_service;

	/**
	 * Database information service.
	 *
	 * @var Database_Service
	 */
	private $database_service;

	/**
	 * Prepared update information.
	 *
	 * @var array
	 */
	private $updates = array();

	/**
	 * Prepared system information.
	 *
	 * @var array
	 */
	private $system = array();

	/**
	 * Prepared database information.
	 *
	 * @var array
	 */
	private $database = array();

	/**
	 * Initialize the Site Status panel.
	 *
	 * @param Update_Service   $update_service   Update data service.
	 * @param System_Service   $system_service   System data service.
	 * @param Database_Service $database_service Database data service.
	 */
	public function __construct(
		Update_Service $update_service,
		System_Service $system_service,
		Database_Service $database_service
	) {
		$this->update_service   = $update_service;
		$this->system_service   = $system_service;
		$this->database_service = $database_service;
	}

	/**
	 * Register the Site Status panel.
	 *
	 * The panel is not registered when no status items are available.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$site_status = $this->get_data();

		if ( empty( $site_status['items'] ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'site-status',
				'title'      => $site_status['title'],
				'icon'       => 'dashicons-performance',
				'width'      => 'full',
				'priority'   => 10,
				'class_name' => 'goug-panel--status',
				'body_view'  => 'dashboard/components/site-status',
				'body_data'  => array(
					'items' => $site_status['items'],
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'site-status',
				),
			)
		);
	}

	/**
	 * Return prepared Site Status data.
	 *
	 * Service data is loaded once and then composed into individual
	 * status-card definitions through focused builder methods.
	 *
	 * @return array
	 */
	private function get_data() {

		$this->updates  = $this->update_service->get_data();
		$this->system   = $this->system_service->get_data();
		$this->database = $this->database_service->get_data();

		$site_status = array(
			'title' => __( 'Site Status', 'goug-framework' ),
			'items' => array(
				$this->build_wordpress_card(),
				$this->build_plugins_card(),
				$this->build_themes_card(),
				$this->build_https_card(),
				$this->build_database_card(),
				$this->build_php_card(),
				$this->build_environment_card(),
			),
		);

		/**
		 * Filter the Site Status panel data.
		 *
		 * @param array $site_status Prepared Site Status data.
		 */
		$site_status = apply_filters(
			'goug_dashboard_site_status',
			$site_status
		);

		return is_array( $site_status )
			? $site_status
			: array();
	}

	/**
	 * Build the WordPress core status card.
	 *
	 * @return array
	 */
	private function build_wordpress_card() {

		$update_count = isset( $this->updates['core'] )
			? (int) $this->updates['core']
			: 0;

		return array(
			'label' => __( 'WordPress', 'goug-framework' ),
			'value' => $update_count > 0
				? __( 'Update available', 'goug-framework' )
				: __( 'Up to date', 'goug-framework' ),
			'meta'  => isset( $this->system['wordpress_version'] )
				? (string) $this->system['wordpress_version']
				: '',
			'icon'  => 'dashicons-wordpress',
			'state' => $update_count > 0
				? 'warning'
				: 'success',
			'url'   => admin_url( 'update-core.php' ),
		);
	}

	/**
	 * Build the plugin-update status card.
	 *
	 * @return array
	 */
	private function build_plugins_card() {

		$update_count = isset( $this->updates['plugins'] )
			? (int) $this->updates['plugins']
			: 0;

		return array(
			'label' => __( 'Plugins', 'goug-framework' ),
			'value' => $this->format_update_count(
				$update_count
			),
			'meta'  => $update_count > 0
				? __( 'View updates', 'goug-framework' )
				: __( 'No updates pending', 'goug-framework' ),
			'icon'  => 'dashicons-admin-plugins',
			'state' => $update_count > 0
				? 'warning'
				: 'success',
			'url'   => admin_url(
				'plugins.php?plugin_status=upgrade'
			),
		);
	}

	/**
	 * Build the theme-update status card.
	 *
	 * @return array
	 */
	private function build_themes_card() {

		$update_count = isset( $this->updates['themes'] )
			? (int) $this->updates['themes']
			: 0;

		return array(
			'label' => __( 'Themes', 'goug-framework' ),
			'value' => $this->format_update_count(
				$update_count
			),
			'meta'  => $update_count > 0
				? __( 'View updates', 'goug-framework' )
				: __( 'No updates pending', 'goug-framework' ),
			'icon'  => 'dashicons-admin-appearance',
			'state' => $update_count > 0
				? 'warning'
				: 'success',
			'url'   => admin_url( 'update-core.php' ),
		);
	}

	/**
	 * Build the HTTPS connection status card.
	 *
	 * @return array
	 */
	private function build_https_card() {

		$is_https = ! empty( $this->system['is_https'] );

		return array(
			'label' => __( 'HTTPS', 'goug-framework' ),
			'value' => $is_https
				? __( 'Secure', 'goug-framework' )
				: __( 'Not secure', 'goug-framework' ),
			'meta'  => $is_https
				? __( 'Encrypted connection', 'goug-framework' )
				: __( 'HTTPS is not active', 'goug-framework' ),
			'icon'  => $is_https
				? 'dashicons-shield-alt'
				: 'dashicons-warning',
			'state' => $is_https
				? 'success'
				: 'warning',
			'url'   => admin_url( 'site-health.php' ),
		);
	}

	/**
	 * Build the database-information status card.
	 *
	 * @return array
	 */
	private function build_database_card() {

		$table_count = isset( $this->database['table_count'] )
			? (int) $this->database['table_count']
			: 0;

		$server = trim(
			sprintf(
				'%1$s %2$s',
				isset( $this->database['server'] )
					? $this->database['server']
					: '',
				isset( $this->database['version'] )
					? $this->database['version']
					: ''
			)
		);

		return array(
			'label' => __( 'Database', 'goug-framework' ),
			'value' => ! empty( $this->database['size'] )
				? $this->database['size']
				: __( 'Unknown', 'goug-framework' ),
			'meta'  => sprintf(
				/* translators: 1: Database table count. 2: Database server name and version. */
				__( '%1$d tables · %2$s', 'goug-framework' ),
				$table_count,
				$server
			),
			'icon'  => 'dashicons-database',
			'state' => 'info',
			'url'   => admin_url(
				'site-health.php?tab=debug'
			),
		);
	}

	/**
	 * Build the PHP runtime status card.
	 *
	 * @return array
	 */
	private function build_php_card() {

		return array(
			'label' => __( 'PHP', 'goug-framework' ),
			'value' => isset( $this->system['php_version'] )
				? (string) $this->system['php_version']
				: '',
			'meta'  => __( 'Runtime version', 'goug-framework' ),
			'icon'  => 'dashicons-editor-code',
			'state' => 'info',
			'url'   => admin_url(
				'site-health.php?tab=debug'
			),
		);
	}

	/**
	 * Build the WordPress environment status card.
	 *
	 * Production environments with debugging enabled are displayed as
	 * warnings because debug output should normally be disabled there.
	 *
	 * @return array
	 */
	private function build_environment_card() {

		$environment = isset( $this->system['environment'] )
			? (string) $this->system['environment']
			: 'production';

		$debug_enabled = ! empty(
			$this->system['debug_enabled']
		);

		$environment_label = ucwords(
			str_replace(
				array( '-', '_' ),
				' ',
				$environment
			)
		);

		return array(
			'label' => __( 'Environment', 'goug-framework' ),
			'value' => $environment_label,
			'meta'  => $debug_enabled
				? __( 'Debug mode enabled', 'goug-framework' )
				: __( 'Debug mode disabled', 'goug-framework' ),
			'icon'  => 'dashicons-admin-site-alt3',
			'state' => (
				'production' === $environment
				&& $debug_enabled
			)
				? 'warning'
				: 'info',
		);
	}

	/**
	 * Format a plugin or theme update count.
	 *
	 * Returns an "Up to date" label when no updates are available.
	 *
	 * @param int $update_count Number of available updates.
	 *
	 * @return string
	 */
	private function format_update_count( $update_count ) {

		$update_count = max(
			0,
			(int) $update_count
		);

		if ( 0 === $update_count ) {
			return __( 'Up to date', 'goug-framework' );
		}

		return sprintf(
			/* translators: %d: Number of available updates. */
			_n(
				'%d update',
				'%d updates',
				$update_count,
				'goug-framework'
			),
			$update_count
		);
	}
}