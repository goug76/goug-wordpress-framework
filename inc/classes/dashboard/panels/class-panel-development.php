<?php
/**
 * Dashboard Development panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Development_Service;

/**
 * Registers the Development dashboard panel.
 */
class Panel_Development implements Dashboard_Panel {

	/**
	 * Development service.
	 *
	 * @var Development_Service
	 */
	private $development_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Development_Service $development_service Development service.
	 */
	public function __construct(
		Development_Service $development_service
	) {
		$this->development_service = $development_service;
	}

	/**
	 * Register the Development panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$development = $this->development_service->get_data();

		if ( empty( $development ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'development',
				'title'      => __( 'Development', 'goug-framework' ),
				'icon'       => 'dashicons-editor-code',
				'priority'   => 80,
				'class_name' => 'goug-panel--development',
				'body_view'  => 'dashboard/components/development',
				'body_data'  => array(
					'development' => $development,
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'development',
				),
			)
		);
	}
}