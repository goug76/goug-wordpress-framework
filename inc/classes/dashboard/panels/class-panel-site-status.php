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
	 * Initialize the panel.
	 *
	 * @param Update_Service $update_service Update data service.
	 * @param System_Service $system_service System data service.
	 */
	public function __construct(
		Update_Service $update_service,
		System_Service $system_service,
		Database_Service $database_service
	) {
		$this->update_service = $update_service;
		$this->system_service = $system_service;
		$this->database_service = $database_service;
	}

	/**
	 * Register the Site Status panel.
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
	 * @return array
	 */
	private function get_data() {

		$updates  = $this->update_service->get_data();
		$system   = $this->system_service->get_data();
		$database = $this->database_service->get_data();

		$environment = ucfirst(
			str_replace(
				array( '-', '_' ),
				' ',
				$system['environment']
			)
		);

		$site_status = array(
			'title' => __( 'Site Status', 'goug-framework' ),
			'items' => array(
				array(
					'label' => __( 'WordPress', 'goug-framework' ),
					'value' => $updates['core'] > 0
						? __( 'Update available', 'goug-framework' )
						: __( 'Up to date', 'goug-framework' ),
					'meta'  => $system['wordpress_version'],
					'icon'  => 'dashicons-wordpress',
					'state' => $updates['core'] > 0
						? 'warning'
						: 'success',
					'url'   => admin_url( 'update-core.php' ),
				),
				array(
					'label' => __( 'Plugins', 'goug-framework' ),
					'value' => $updates['plugins'] > 0
						? sprintf(
							/* translators: %d: Number of plugin updates. */
							_n(
								'%d update',
								'%d updates',
								$updates['plugins'],
								'goug-framework'
							),
							$updates['plugins']
						)
						: __( 'Up to date', 'goug-framework' ),
					'meta'  => $updates['plugins'] > 0
						? __( 'View updates', 'goug-framework' )
						: __( 'No updates pending', 'goug-framework' ),
					'icon'  => 'dashicons-admin-plugins',
					'state' => $updates['plugins'] > 0
						? 'warning'
						: 'success',
					'url'   => admin_url(
						'plugins.php?plugin_status=upgrade'
					),
				),
				array(
					'label' => __( 'Themes', 'goug-framework' ),
					'value' => $updates['themes'] > 0
						? sprintf(
							/* translators: %d: Number of theme updates. */
							_n(
								'%d update',
								'%d updates',
								$updates['themes'],
								'goug-framework'
							),
							$updates['themes']
						)
						: __( 'Up to date', 'goug-framework' ),
					'meta'  => $updates['themes'] > 0
						? __( 'View updates', 'goug-framework' )
						: __( 'No updates pending', 'goug-framework' ),
					'icon'  => 'dashicons-admin-appearance',
					'state' => $updates['themes'] > 0
						? 'warning'
						: 'success',
					'url'   => admin_url( 'update-core.php' ),
				),
				array(
					'label' => __( 'HTTPS', 'goug-framework' ),
					'value' => $system['is_https']
						? __( 'Secure', 'goug-framework' )
						: __( 'Not secure', 'goug-framework' ),
					'meta'  => $system['is_https']
						? __( 'Encrypted connection', 'goug-framework' )
						: __( 'HTTPS is not active', 'goug-framework' ),
					'icon'  => $system['is_https']
						? 'dashicons-shield-alt'
						: 'dashicons-warning',
					'state' => $system['is_https']
						? 'success'
						: 'warning',
					'url'   => admin_url( 'site-health.php' ),
				),
				array(
					'label' => __( 'Database', 'goug-framework' ),
					'value' => ! empty( $database['size'] )
						? $database['size']
						: __( 'Unknown', 'goug-framework' ),
					'meta'  => sprintf(
						/* translators: 1: Database table count. 2: Database server name and version. */
						__( '%1$d tables · %2$s', 'goug-framework' ),
						isset( $database['table_count'] )
							? (int) $database['table_count']
							: 0,
						trim(
							sprintf(
								'%1$s %2$s',
								$database['server'] ?? '',
								$database['version'] ?? ''
							)
						)
					),
					'icon'  => 'dashicons-database',
					'state' => 'info',
					'url'   => admin_url(
						'site-health.php?tab=debug'
					),
				),
				array(
					'label' => __( 'PHP', 'goug-framework' ),
					'value' => $system['php_version'],
					'meta'  => __( 'Runtime version', 'goug-framework' ),
					'icon'  => 'dashicons-editor-code',
					'state' => 'info',
					'url'   => admin_url(
						'site-health.php?tab=debug'
					),
				),
				array(
					'label' => __( 'Environment', 'goug-framework' ),
					'value' => $environment,
					'meta'  => $system['debug_enabled']
						? __( 'Debug mode enabled', 'goug-framework' )
						: __( 'Debug mode disabled', 'goug-framework' ),
					'icon'  => 'dashicons-admin-site-alt3',
					'state' => (
						'production' === $system['environment'] &&
						$system['debug_enabled']
					)
						? 'warning'
						: 'info',
				),
			),
		);

		/**
		 * Filter the Site Status panel data.
		 *
		 * @param array $site_status Prepared Site Status data.
		 */
		return apply_filters(
			'goug_dashboard_site_status',
			$site_status
		);
	}
}