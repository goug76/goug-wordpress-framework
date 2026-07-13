<?php
/**
 * Dashboard At a Glance panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Content_Service;

/**
 * Registers and prepares the At a Glance dashboard panel.
 */
class Panel_At_A_Glance implements Dashboard_Panel {

	/**
	 * Content data service.
	 *
	 * @var Content_Service
	 */
	private $content_service;

	/**
	 * Initialize the panel.
	 *
	 * @param Content_Service $content_service Content data service.
	 */
	public function __construct( Content_Service $content_service ) {

		$this->content_service = $content_service;
	}

	/**
	 * Register the At a Glance panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$items = $this->get_items();

		if ( empty( $items ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'at-a-glance',
				'title'      => __( 'At a Glance', 'goug-framework' ),
				'icon'       => 'dashicons-visibility',
				'priority'   => 30,
				'class_name' => 'goug-panel--at-a-glance',
				'body_view'  => 'dashboard/components/at-a-glance',
				'body_data'  => array(
					'items' => $items,
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'at-a-glance',
				),
			)
		);
	}

	/**
	 * Return prepared At a Glance statistics.
	 *
	 * @return array
	 */
	private function get_items() {

		$counts = $this->content_service->get_data();

		$items = array(
			array(
				'label' => __( 'Posts', 'goug-framework' ),
				'value' => $counts['posts']['published'],
				'icon'  => 'dashicons-admin-post',
			),
			array(
				'label' => __( 'Pages', 'goug-framework' ),
				'value' => $counts['pages']['published'],
				'icon'  => 'dashicons-admin-page',
			),
		);

		if ( isset( $counts['courses'] ) ) {
			$items[] = array(
				'label' => __( 'Courses', 'goug-framework' ),
				'value' => $counts['courses']['published'],
				'icon'  => 'dashicons-welcome-learn-more',
			);
		}

		$draft_count =
			$counts['posts']['drafts']
			+ $counts['pages']['drafts'];

		if ( isset( $counts['courses'] ) ) {
			$draft_count += $counts['courses']['drafts'];
		}

		$items[] = array(
			'label' => __( 'Drafts', 'goug-framework' ),
			'value' => $draft_count,
			'icon'  => 'dashicons-edit-page',
		);

		$items[] = array(
			'label'     => __( 'Comments', 'goug-framework' ),
			'value'     => $counts['comments']['approved'],
			'icon'      => 'dashicons-admin-comments',
			'secondary' => $counts['comments']['pending'] > 0
				? sprintf(
					/* translators: %d: Pending comment count. */
					_n(
						'%d pending',
						'%d pending',
						$counts['comments']['pending'],
						'goug-framework'
					),
					$counts['comments']['pending']
				)
				: '',
			'state'     => $counts['comments']['pending'] > 0
				? 'warning'
				: 'default',
		);

		$items[] = array(
			'label' => __( 'Users', 'goug-framework' ),
			'value' => $counts['users'],
			'icon'  => 'dashicons-admin-users',
		);

		$items[] = array(
			'label' => __( 'Media Files', 'goug-framework' ),
			'value' => $counts['media']['total'],
			'icon'  => 'dashicons-admin-media',
		);

		/**
		 * Filter At a Glance statistics.
		 *
		 * @param array $items Prepared statistics.
		 */
		$items = apply_filters(
			'goug_dashboard_at_a_glance_items',
			$items
		);

		return is_array( $items )
			? $items
			: array();
	}
}