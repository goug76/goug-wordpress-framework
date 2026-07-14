<?php
/**
 * Dashboard Storage Usage panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Storage_Service;

/**
 * Registers the Storage Usage dashboard panel.
 */
class Panel_Storage_Usage implements Dashboard_Panel {

	/**
	 * Storage service.
	 *
	 * @var Storage_Service
	 */
	private $storage_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Storage_Service $storage_service Storage service.
	 */
	public function __construct(
		Storage_Service $storage_service
	) {
		$this->storage_service = $storage_service;
	}

	/**
	 * Register the Storage Usage panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$storage = $this->storage_service->get_data();

		if ( empty( $storage['items'] ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'storage-usage',
				'title'      => __( 'Storage Usage', 'goug-framework' ),
				'icon'       => 'dashicons-database',
				'row'      	 => 2,
				'width'    	 => 'third',
				'priority' 	 => 30,
				'class_name' => 'goug-panel--storage-usage',
				'body_view'  => 'dashboard/components/storage-usage',
				'body_data'  => array(
					'storage' => $storage,
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'storage-usage',
				),
			)
		);
	}
}