<?php
/**
 * Dashboard data provider.
 *
 * Collects lightweight, native WordPress data required by the
 * custom admin dashboard.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Services\Site_Service;
use GOUG\Inc\Dashboard\Services\User_Service;
use GOUG\Inc\Dashboard\Services\Update_Service;
use GOUG\Inc\Dashboard\Services\System_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Site_Status;
use GOUG\Inc\Dashboard\Panels\Panel_Quick_Actions;
use GOUG\Inc\Dashboard\Panels\Panel_Site_Overview;
use GOUG\Inc\Dashboard\Services\Content_Service;

/**
 * Provides data for the custom admin dashboard.
 *
 * This class is intentionally not a singleton. It does not register
 * WordPress hooks or maintain global state; it simply gathers data
 * when the dashboard coordinator requests it.
 */
class Dashboard_Data {

	/**
	 * Dashboard panel registry.
	 *
	 * @var Dashboard_Registry
	 */
	private $registry;

	/**
	 * Whether default panels have been registered.
	 *
	 * @var bool
	 */
	private $panels_registered = false;

	/**
	 * Site data service.
	 *
	 * @var Site_Service
	 */
	private $site_service;

	/**
	 * User data service.
	 *
	 * @var User_Service
	 */
	private $user_service;

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
	 * Site Status panel module.
	 *
	 * @var Panel_Site_Status
	 */
	private $site_status_panel;

	/**
	 * Quick Actions panel module.
	 *
	 * @var Panel_Quick_Actions
	 */
	private $quick_actions_panel;

	/**
	 * Content data service.
	 *
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * Site Overview panel module.
	 *
	 * @var Panel_Site_Overview
	 */
	private $site_overview_panel;

	/**
	 * Initialize the dashboard data provider.
	 */
	public function __construct() {

		$this->registry     = new Dashboard_Registry();
		$this->site_service = new Site_Service();
		$this->user_service = new User_Service();
		$this->update_service = new Update_Service();
		$this->system_service = new System_Service();
		$this->content_service = new Content_Service();

		$this->site_overview_panel = new Panel_Site_Overview(
			$this->content_service
		);

		$this->site_status_panel = new Panel_Site_Status(
			$this->update_service,
			$this->system_service
		);

		$this->quick_actions_panel = new Panel_Quick_Actions();
	}

	/**
	 * Return all currently supported dashboard data.
	 *
	 * @return array
	 */
	public function get_data() {

		$this->register_default_panels();

		return array(
			'site'           => $this->site_service->get_data(),
			'user'           => $this->user_service->get_data(),
			'counts'         => $this->content_service->get_data(),
			'updates'        => $this->update_service->get_data(),
			'system'         => $this->system_service->get_data(),
			'system_updates' => $this->get_system_updates_data(),

			/*
			* get_panels() belongs to Dashboard_Registry.
			*/
			'panels'         => $this->registry->get_panels(),
		);
	}

	/**
	 * Register the default dashboard panels.
	 *
	 * @return void
	 */
	private function register_default_panels() {

		if ( $this->panels_registered ) {
			return;
		}

		$this->panels_registered = true;

		$this->site_status_panel->register(
			$this->registry
		);

		$this->quick_actions_panel->register(
			$this->registry
		);

		$this->site_overview_panel->register(
			$this->registry
		);

		/**
		 * Fires after default dashboard panels are registered.
		 *
		 * @param Dashboard_Registry $registry Dashboard registry.
		 */
		do_action(
			'goug_dashboard_register_panels',
			$this->registry
		);
	}

	/**
	 * Return prepared System Updates card data.
	 *
	 * @return array
	 */
	private function get_system_updates_data() {

		$updates = $this->update_service->get_data();

		$system_updates = array(
			'title' => __( 'System Updates', 'goug-framework' ),
			'items' => array(
				array(
					'title'  => __( 'WordPress Core', 'goug-framework' ),
					'icon'   => 'dashicons-wordpress',
					'url'    => admin_url( 'update-core.php' ),
					'count'  => $updates['core'],
					'status' => $updates['core'] > 0
						? __( 'Update Available', 'goug-framework' )
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['core'] > 0
						? 'warning'
						: 'success',
				),
				array(
					'title'  => __( 'Plugins', 'goug-framework' ),
					'icon'   => 'dashicons-admin-plugins',
					'url'    => admin_url( 'plugins.php?plugin_status=upgrade' ),
					'count'  => $updates['plugins'],
					'status' => $updates['plugins'] > 0
						? sprintf(
							/* translators: %d: Number of plugin updates. */
							_n(
								'%d update available',
								'%d updates available',
								$updates['plugins'],
								'goug-framework'
							),
							$updates['plugins']
						)
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['plugins'] > 0
						? 'warning'
						: 'success',
				),
				array(
					'title'  => __( 'Themes', 'goug-framework' ),
					'icon'   => 'dashicons-admin-appearance',
					'url'    => admin_url( 'update-core.php' ),
					'count'  => $updates['themes'],
					'status' => $updates['themes'] > 0
						? sprintf(
							/* translators: %d: Number of theme updates. */
							_n(
								'%d update available',
								'%d updates available',
								$updates['themes'],
								'goug-framework'
							),
							$updates['themes']
						)
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['themes'] > 0
						? 'warning'
						: 'success',
				),
			),
		);

		/*
		* Translation updates are normally handled automatically and are not
		* prominent enough to occupy a permanent dashboard card. Display the
		* card only when translation updates are actually pending.
		*/
		if ( $updates['translations'] > 0 ) {
			$system_updates['items'][] = array(
				'title'  => __( 'Translations', 'goug-framework' ),
				'icon'   => 'dashicons-translation',
				'url'    => admin_url( 'update-core.php' ),
				'count'  => $updates['translations'],
				'status' => sprintf(
					/* translators: %d: Number of translation updates. */
					_n(
						'%d update available',
						'%d updates available',
						$updates['translations'],
						'goug-framework'
					),
					$updates['translations']
				),
				'state'  => 'warning',
			);
		}

		/**
		 * Filter the System Updates section.
		 *
		 * @param array $system_updates Prepared System Updates data.
		 */
		return apply_filters(
			'goug_dashboard_system_updates',
			$system_updates
		);
	}

}