<?php
/**
 * Dashboard Site Overview panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\Content_Service;

/**
 * Registers and prepares the Site Overview dashboard panel.
 */
class Panel_Site_Overview implements Dashboard_Panel {

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
	 * Register the Site Overview panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$overview = $this->get_data();

		if ( empty( $overview['items'] ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'site-overview',
				'title'      => $overview['title'],
				'icon'       => 'dashicons-chart-bar',
				'priority'   => 30,
				'class_name' => 'goug-panel--overview',
				'body_view'  => 'components/card-grid',
				'body_data'  => array(
					'items'      => $overview['items'],
					'class_name' => 'goug-card-grid--metrics',
					'card_class' => 'goug-card--metric',
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'site-overview',
				),
			)
		);
	}

	/**
	 * Return prepared Site Overview data.
	 *
	 * @return array
	 */
	private function get_data() {

		$counts = $this->content_service->get_data();

		$overview = array(
			'title' => __( 'Site Overview', 'goug-framework' ),
			'items' => array(
				array(
					'title' => sprintf(
						/* translators: %d: Published post count. */
						_n(
							'%d Post',
							'%d Posts',
							$counts['posts']['published'],
							'goug-framework'
						),
						$counts['posts']['published']
					),
					'icon'        => 'dashicons-admin-post',
					'url'         => admin_url( 'edit.php' ),
					'capability'  => 'edit_posts',
					'description' => __(
						'Published posts',
						'goug-framework'
					),
				),
				array(
					'title' => sprintf(
						/* translators: %d: Published page count. */
						_n(
							'%d Page',
							'%d Pages',
							$counts['pages']['published'],
							'goug-framework'
						),
						$counts['pages']['published']
					),
					'icon'        => 'dashicons-admin-page',
					'url'         => admin_url(
						'edit.php?post_type=page'
					),
					'capability'  => 'edit_pages',
					'description' => __(
						'Published pages',
						'goug-framework'
					),
				),
				array(
					'title' => sprintf(
						/* translators: %d: Approved comment count. */
						_n(
							'%d Comment',
							'%d Comments',
							$counts['comments']['approved'],
							'goug-framework'
						),
						$counts['comments']['approved']
					),
					'icon'        => 'dashicons-admin-comments',
					'url'         => admin_url( 'edit-comments.php' ),
					'capability'  => 'moderate_comments',
					'description' => $counts['comments']['pending'] > 0
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
						: __(
							'No pending comments',
							'goug-framework'
						),
					'badge' => $counts['comments']['pending'] > 0
						? (string) $counts['comments']['pending']
						: '',
				),
				array(
					'title' => sprintf(
						/* translators: %d: Registered user count. */
						_n(
							'%d User',
							'%d Users',
							$counts['users'],
							'goug-framework'
						),
						$counts['users']
					),
					'icon'        => 'dashicons-admin-users',
					'url'         => admin_url( 'users.php' ),
					'capability'  => 'list_users',
					'description' => __(
						'Registered users',
						'goug-framework'
					),
				),
			),
		);

		$overview = apply_filters(
			'goug_dashboard_overview',
			$overview
		);

		return $this->filter_items( $overview );
	}

	/**
	 * Remove hidden or inaccessible overview cards.
	 *
	 * @param array $overview Site Overview configuration.
	 *
	 * @return array
	 */
	private function filter_items( $overview ) {

		if (
			! is_array( $overview ) ||
			empty( $overview['items'] ) ||
			! is_array( $overview['items'] )
		) {
			return array();
		}

		$overview['items'] = array_values(
			array_filter(
				$overview['items'],
				static function ( $item ) {

					if (
						! is_array( $item ) ||
						empty( $item['title'] )
					) {
						return false;
					}

					if (
						isset( $item['visible'] ) &&
						false === (bool) $item['visible']
					) {
						return false;
					}

					$capability = ! empty( $item['capability'] )
						? $item['capability']
						: 'read';

					return current_user_can( $capability );
				}
			)
		);

		if ( empty( $overview['items'] ) ) {
			return array();
		}

		return $overview;
	}
}