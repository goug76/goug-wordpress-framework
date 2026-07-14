<?php
/**
 * Dashboard data coordinator.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Panels\Dashboard_Panel;
use GOUG\Inc\Dashboard\Panels\Panel_At_A_Glance;
use GOUG\Inc\Dashboard\Panels\Panel_Development;
use GOUG\Inc\Dashboard\Panels\Panel_Quick_Actions;
use GOUG\Inc\Dashboard\Panels\Panel_Quick_Draft;
use GOUG\Inc\Dashboard\Panels\Panel_Recent_Activity;
use GOUG\Inc\Dashboard\Panels\Panel_Site_Health;
use GOUG\Inc\Dashboard\Panels\Panel_Site_Status;
use GOUG\Inc\Dashboard\Panels\Panel_Storage_Usage;
use GOUG\Inc\Dashboard\Services\Activity_Service;
use GOUG\Inc\Dashboard\Services\Content_Service;
use GOUG\Inc\Dashboard\Services\Database_Service;
use GOUG\Inc\Dashboard\Services\Development_Service;
use GOUG\Inc\Dashboard\Services\Draft_Service;
use GOUG\Inc\Dashboard\Services\Health_Service;
use GOUG\Inc\Dashboard\Services\Quick_Actions_Service;
use GOUG\Inc\Dashboard\Services\Site_Service;
use GOUG\Inc\Dashboard\Services\Storage_Service;
use GOUG\Inc\Dashboard\Services\System_Service;
use GOUG\Inc\Dashboard\Services\Update_Service;
use GOUG\Inc\Dashboard\Services\User_Service;

/**
 * Coordinates dashboard services and panel registration.
 *
 * Responsibilities:
 *
 * - Create services shared by the dashboard.
 * - Connect prepared data services to their panel modules.
 * - Register the default dashboard panels.
 * - Return top-level data consumed by the dashboard template.
 *
 * This coordinator does not render HTML or perform dashboard data
 * calculations itself.
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
	 * Basic site information service.
	 *
	 * @var Site_Service
	 */
	private $site_service;

	/**
	 * Current-user information service.
	 *
	 * @var User_Service
	 */
	private $user_service;

	/**
	 * WordPress update information service.
	 *
	 * @var Update_Service
	 */
	private $update_service;

	/**
	 * WordPress system information service.
	 *
	 * @var System_Service
	 */
	private $system_service;

	/**
	 * WordPress content-count service.
	 *
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * Default dashboard panel modules.
	 *
	 * Panels are stored in their default registration order. The
	 * registry applies row and priority sorting before rendering.
	 *
	 * @var Dashboard_Panel[]
	 */
	private $panels = array();

	/**
	 * Initialize the dashboard coordinator.
	 *
	 * Draft_Service is injected because its AJAX hooks are registered
	 * outside the dashboard rendering request and must use the same
	 * service instance.
	 *
	 * @param Draft_Service $draft_service Quick Draft service.
	 */
	public function __construct( Draft_Service $draft_service ) {

		$this->registry        = new Dashboard_Registry();
		$this->site_service    = new Site_Service();
		$this->user_service    = new User_Service();
		$this->update_service  = new Update_Service();
		$this->system_service  = new System_Service();
		$this->content_service = new Content_Service();

		$this->panels = $this->build_default_panels(
			$draft_service
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
			'site'    => $this->site_service->get_data(),
			'user'    => $this->user_service->get_data(),
			'counts'  => $this->content_service->get_data(),
			'updates' => $this->update_service->get_data(),
			'system'  => $this->system_service->get_data(),
			'panels'  => $this->registry->get_panels(),
		);
	}

	/**
	 * Build the default dashboard panel collection.
	 *
	 * Services used only by panels remain local to this composition
	 * method. Their panel instances retain the required references.
	 *
	 * @param Draft_Service $draft_service Quick Draft service.
	 *
	 * @return Dashboard_Panel[]
	 */
	private function build_default_panels(
		Draft_Service $draft_service
	) {

		$database_service = new Database_Service();

		$storage_service = new Storage_Service(
			$database_service
		);

		return array(
			new Panel_Site_Status(
				$this->update_service,
				$this->system_service,
				$database_service
			),

			new Panel_Site_Health(
				new Health_Service()
			),

			new Panel_At_A_Glance(
				$this->content_service
			),

			new Panel_Storage_Usage(
				$storage_service
			),

			new Panel_Quick_Actions(
				new Quick_Actions_Service()
			),

			new Panel_Recent_Activity(
				new Activity_Service( 12 )
			),

			new Panel_Quick_Draft(
				$draft_service
			),

			new Panel_Development(
				new Development_Service()
			),
		);
	}

	/**
	 * Register the default dashboard panels.
	 *
	 * Each panel is registered once per dashboard request. The registry
	 * handles availability, capability checks, row ordering, and panel
	 * priority.
	 *
	 * @return void
	 */
	private function register_default_panels() {

		if ( $this->panels_registered ) {
			return;
		}

		$this->panels_registered = true;

		foreach ( $this->panels as $panel ) {

			if ( ! $panel instanceof Dashboard_Panel ) {
				continue;
			}

			$panel->register(
				$this->registry
			);
		}

		/**
		 * Fires after default dashboard panels are registered.
		 *
		 * Third-party code may register, remove, or modify panels
		 * through the provided registry.
		 *
		 * @param Dashboard_Registry $registry Dashboard registry.
		 */
		do_action(
			'goug_dashboard_register_panels',
			$this->registry
		);
	}
}