<?php
/**
 * Dashboard Quick Draft panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Draft_Service;

/**
 * Registers the Quick Draft dashboard panel.
 */
class Panel_Quick_Draft implements Dashboard_Panel {

	/**
	 * Draft service.
	 *
	 * @var Draft_Service
	 */
	private $draft_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Draft_Service $draft_service Draft service.
	 */
	public function __construct(
		Draft_Service $draft_service
	) {
		$this->draft_service = $draft_service;
	}

	/**
	 * Register the Quick Draft panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$form_data = $this->draft_service->get_form_data();

		if ( empty( $form_data['can_create'] ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'quick-draft',
				'title'      => __( 'Quick Draft', 'goug-framework' ),
				'icon'       => 'dashicons-edit',
				'row'      	 => 4,
				'width'    	 => 'full',
				'priority' 	 => 10,
				'class_name' => 'goug-panel--quick-draft',
				'body_view'  => 'dashboard/components/quick-draft',
				'body_data'  => $form_data,
				'capability' => 'edit_posts',
				'attributes' => array(
					'data-panel-id' => 'quick-draft',
				),
			)
		);
	}
}