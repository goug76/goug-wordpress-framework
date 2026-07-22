<?php
/**
 * Dashboard Recent Activity panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Activity_Service;

/**
 * Registers the Recent Activity dashboard panel.
 */
class Panel_Recent_Activity implements Dashboard_Panel {

	/**
	 * Activity data service.
	 *
	 * @var Activity_Service
	 */
	private $activity_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Activity_Service $activity_service Activity service.
	 */
	public function __construct(
		Activity_Service $activity_service
	) {
		$this->activity_service = $activity_service;
	}

	/**
	 * Register the Recent Activity panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$items = $this->activity_service->get_data();

		if ( empty( $items ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'recent-activity',
				'title'      => __( 'Recent Activity', 'goug-framework' ),
				'icon'       => 'dashicons-backup',
				'width'    	 => 'half',
				'priority' 	 => 60,
				'class_name' => 'goug-panel--recent-activity',
				'body_view'  => 'dashboard/components/recent-activity',
				'body_data'  => array(
					'items' => $items,
				),
				'capability' => 'read',
				'attributes' => array(
					'data-panel-id' => 'recent-activity',
				),
			)
		);
	}
}