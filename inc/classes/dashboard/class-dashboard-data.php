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
use GOUG\Inc\Dashboard\Services\Quick_Actions_Service;
use GOUG\Inc\Dashboard\Panels\Panel_At_A_Glance;
use GOUG\Inc\Dashboard\Services\Content_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Recent_Activity;
use GOUG\Inc\Dashboard\Services\Activity_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Quick_Draft;
use GOUG\Inc\Dashboard\Services\Draft_Service;
use GOUG\Inc\Dashboard\Services\Database_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Site_Health;
use GOUG\Inc\Dashboard\Services\Health_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Storage_Usage;
use GOUG\Inc\Dashboard\Services\Storage_Service;
use GOUG\Inc\Dashboard\Panels\Panel_Development;
use GOUG\Inc\Dashboard\Services\Development_Service;

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
	 * Quick Actions service.
	 *
	 * @var Quick_Actions_Service
	 */
	private $quick_actions_service;

	/**
	 * Content data service.
	 *
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * At a Glance panel module.
	 *
	 * @var Panel_At_A_Glance
	 */
	private $at_a_glance_panel;

	/**
	 * Activity data service.
	 *
	 * @var Activity_Service
	 */
	private $activity_service;

	/**
	 * Recent Activity panel module.
	 *
	 * @var Panel_Recent_Activity
	 */
	private $recent_activity_panel;

	/**
	 * Quick Draft service.
	 *
	 * @var Draft_Service
	 */
	private $draft_service;

	/**
	 * Quick Draft panel.
	 *
	 * @var Panel_Quick_Draft
	 */
	private $quick_draft_panel;

	/**
	 * Database information service.
	 *
	 * @var Database_Service
	 */
	private $database_service;

	private $health_service;
	private $site_health_panel;

	/**
	 * Storage usage service.
	 *
	 * @var Storage_Service
	 */
	private $storage_service;

	/**
	 * Storage Usage panel.
	 *
	 * @var Panel_Storage_Usage
	 */
	private $storage_usage_panel;

	/**
	 * Development information service.
	 *
	 * @var Development_Service
	 */
	private $development_service;

	/**
	 * Development panel.
	 *
	 * @var Panel_Development
	 */
	private $development_panel;

	/**
	 * Initialize the dashboard data provider.
	 */
	public function __construct( Draft_Service $draft_service ) {
		$this->draft_service = $draft_service;

		$this->registry     = new Dashboard_Registry();
		$this->site_service = new Site_Service();
		$this->user_service = new User_Service();
		$this->update_service = new Update_Service();
		$this->system_service = new System_Service();
		$this->content_service = new Content_Service();
		$this->database_service = new Database_Service();
		$this->health_service = new Health_Service();		
		$this->quick_actions_service = new Quick_Actions_Service();

		$this->storage_service = new Storage_Service(
			$this->database_service
		);

		$this->storage_usage_panel = new Panel_Storage_Usage(
			$this->storage_service
		);

		$this->at_a_glance_panel = new Panel_At_A_Glance(
			$this->content_service
		);

		$this->site_status_panel = new Panel_Site_Status(
			$this->update_service,
			$this->system_service,
			$this->database_service
		);

		$this->quick_actions_panel = new Panel_Quick_Actions(
			$this->quick_actions_service
		);

		$this->activity_service = new Activity_Service( 12 );

		$this->recent_activity_panel = new Panel_Recent_Activity(
			$this->activity_service
		);

		$this->quick_draft_panel = new Panel_Quick_Draft(
			$this->draft_service
		);

		$this->site_health_panel = new Panel_Site_Health(
			$this->health_service
		);

		$this->development_service = new Development_Service();

		$this->development_panel = new Panel_Development(
			$this->development_service
		);
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

		$this->at_a_glance_panel->register(
			$this->registry
		);

		$this->at_a_glance_panel->register(
			$this->registry
		);

		$this->recent_activity_panel->register(
			$this->registry
		);

		$this->quick_draft_panel->register(
			$this->registry
		);

		$this->site_health_panel->register(
			$this->registry
		);

		$this->site_health_panel->register(
			$this->registry
		);

		$this->storage_usage_panel->register(
			$this->registry
		);

		$this->storage_usage_panel->register(
			$this->registry
		);

		$this->development_panel->register(
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

}