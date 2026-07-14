<?php
/**
 * Dashboard Site Health panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Health_Service;

/**
 * Registers the Site Health dashboard panel.
 */
class Panel_Site_Health implements Dashboard_Panel {

	/**
	 * Site Health service.
	 *
	 * @var Health_Service
	 */
	private $health_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Health_Service $health_service Site Health service.
	 */
	public function __construct( Health_Service $health_service ) {

		$this->health_service = $health_service;
	}

	/**
	 * Register the Site Health panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$health = $this->health_service->get_data();

		if ( empty( $health ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'site-health',
				'title'      => __( 'Site Health', 'goug-framework' ),
				'icon'       => 'dashicons-heart',
				'priority'   => 60,
				'class_name' => 'goug-panel--site-health',
				'body_view'  => 'dashboard/components/site-health',
				'body_data'  => array(
					'health' => $health,
				),
				'capability' => 'view_site_health_checks',
				'attributes' => array(
					'data-panel-id' => 'site-health',
				),
			)
		);
	}
}