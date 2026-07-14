<?php
/**
 * Dashboard Quick Actions panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Quick_Actions_Service;

/**
 * Registers the Quick Actions dashboard panel.
 *
 * Responsibilities:
 *
 * - Request prepared action groups from Quick_Actions_Service.
 * - Register Quick Actions panel metadata.
 * - Pass prepared group data to the dashboard view.
 *
 * Action definitions, visibility, and capability filtering belong to
 * the service rather than this panel registration class.
 */
class Panel_Quick_Actions implements Dashboard_Panel {

	/**
	 * Quick Actions service.
	 *
	 * @var Quick_Actions_Service
	 */
	private $quick_actions_service;

	/**
	 * Initialize the Quick Actions panel.
	 *
	 * @param Quick_Actions_Service $quick_actions_service
	 *        Quick Actions service.
	 */
	public function __construct(
		Quick_Actions_Service $quick_actions_service
	) {
		$this->quick_actions_service = $quick_actions_service;
	}

	/**
	 * Register the Quick Actions panel.
	 *
	 * The panel is not registered when the current user has no
	 * available action groups.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$groups = $this->quick_actions_service->get_data();

		if ( empty( $groups ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'quick-actions',
				'title'      => __(
					'Quick Actions',
					'goug-framework'
				),
				'icon_svg'   => 'lightning.svg',
				'row'        => 3,
				'width'      => 'half',
				'priority'   => 10,
				'class_name' => 'goug-panel--quick-actions',
				'body_view'  => 'dashboard/components/quick-actions',
				'body_data'  => array(
					'groups' => $groups,
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'quick-actions',
				),
			)
		);
	}
}