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
	 * Cached content counts for the current request.
	 *
	 * @var array|null
	 */
	private $content_counts = null;

	/**
	 * Initialize the dashboard data provider.
	 */
	public function __construct() {

		$this->registry     = new Dashboard_Registry();
		$this->site_service = new Site_Service();
		$this->user_service = new User_Service();
		$this->update_service = new Update_Service();
		$this->system_service = new System_Service();

		$this->site_status_panel = new Panel_Site_Status(
			$this->update_service,
			$this->system_service
		);

		$this->quick_actions_panel = new Panel_Quick_Actions();
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
			'counts'         => $this->get_content_counts(),
			'updates'        => $this->update_service->get_data(),
			'system'         => $this->system_service->get_data(),
			'overview'       => $this->get_overview_data(),
			'system_updates' => $this->get_system_updates_data(),

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

		$overview    = $this->get_overview_data();

		$quick_action_groups = array_filter(
			array(
				$actions['essential'] ?? array(),
				$actions['settings'] ?? array(),
				$actions['design'] ?? array(),
			)
		);

		$this->site_status_panel->register(
			$this->registry
		);

		$this->quick_actions_panel->register(
			$this->registry
		);

		if ( ! empty( $overview['items'] ) ) {
			$this->registry->register_panel(
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
		 * Fires after default dashboard panels are registered.
		 *
		 * External code may register or unregister panels here.
		 *
		 * @param Dashboard_Registry $registry Dashboard registry.
		 */
		do_action(
			'goug_dashboard_register_panels',
			$this->registry
		);
	}

	/**
	 * Return WordPress content and user counts.
	 *
	 * These functions use WordPress APIs and internal caches rather
	 * than running custom database queries.
	 *
	 * @return array
	 */
	private function get_content_counts() {

		if ( null !== $this->content_counts ) {
			return $this->content_counts;
		}

		$post_counts    = wp_count_posts( 'post' );
		$page_counts    = wp_count_posts( 'page' );
		$comment_counts = wp_count_comments();
		$user_counts    = count_users();

		$this->content_counts = array(
			'posts' => array(
				'published' => isset( $post_counts->publish )
					? (int) $post_counts->publish
					: 0,
				'drafts'    => isset( $post_counts->draft )
					? (int) $post_counts->draft
					: 0,
			),
			'pages' => array(
				'published' => isset( $page_counts->publish )
					? (int) $page_counts->publish
					: 0,
				'drafts'    => isset( $page_counts->draft )
					? (int) $page_counts->draft
					: 0,
			),
			'comments' => array(
				'total'    => isset( $comment_counts->total_comments )
					? (int) $comment_counts->total_comments
					: 0,
				'approved' => isset( $comment_counts->approved )
					? (int) $comment_counts->approved
					: 0,
				'pending'  => isset( $comment_counts->moderated )
					? (int) $comment_counts->moderated
					: 0,
				'spam'     => isset( $comment_counts->spam )
					? (int) $comment_counts->spam
					: 0,
			),
			'users' => isset( $user_counts['total_users'] )
				? (int) $user_counts['total_users']
				: 0,
		);

		return $this->content_counts;
	}

	/**
	 * Return Site Overview card data.
	 *
	 * @return array
	 */
	private function get_overview_data() {

		$counts = $this->get_content_counts();

		$overview = array(
			'title' => __( 'Site Overview', 'goug-framework' ),
			'items' => array(
				array(
					'title'       => sprintf(
						/* translators: %d: Number of published posts. */
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
					'description' => __( 'Published posts', 'goug-framework' ),
				),
				array(
					'title'       => sprintf(
						/* translators: %d: Number of published pages. */
						_n(
							'%d Page',
							'%d Pages',
							$counts['pages']['published'],
							'goug-framework'
						),
						$counts['pages']['published']
					),
					'icon'        => 'dashicons-admin-page',
					'url'         => admin_url( 'edit.php?post_type=page' ),
					'capability'  => 'edit_pages',
					'description' => __( 'Published pages', 'goug-framework' ),
				),
				array(
					'title'       => sprintf(
						/* translators: %d: Number of approved comments. */
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
							/* translators: %d: Number of pending comments. */
							_n(
								'%d pending',
								'%d pending',
								$counts['comments']['pending'],
								'goug-framework'
							),
							$counts['comments']['pending']
						)
						: __( 'No pending comments', 'goug-framework' ),
					'badge'       => $counts['comments']['pending'] > 0
						? (string) $counts['comments']['pending']
						: '',
				),
				array(
					'title'       => sprintf(
						/* translators: %d: Number of registered users. */
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
					'description' => __( 'Registered users', 'goug-framework' ),
				),
			),
		);

		/**
		 * Filter Site Overview card data.
		 *
		 * @param array $overview Site Overview configuration.
		 */
		$overview = apply_filters(
			'goug_dashboard_overview',
			$overview
		);

		return $this->filter_overview_data( $overview );
	}

	/**
	 * Remove hidden or inaccessible Site Overview cards.
	 *
	 * @param array $overview Site Overview configuration.
	 *
	 * @return array
	 */
	private function filter_overview_data( $overview ) {

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

	/**
	 * Return prepared System Updates card data.
	 *
	 * @return array
	 */
	private function get_system_updates_data() {

		$updates = $this->update_service->get_data();

		$system_updates = array(
			'title' => __( 'System Updates', 'goug-framework' ),
			'items' => array(
				array(
					'title'  => __( 'WordPress Core', 'goug-framework' ),
					'icon'   => 'dashicons-wordpress',
					'url'    => admin_url( 'update-core.php' ),
					'count'  => $updates['core'],
					'status' => $updates['core'] > 0
						? __( 'Update Available', 'goug-framework' )
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['core'] > 0
						? 'warning'
						: 'success',
				),
				array(
					'title'  => __( 'Plugins', 'goug-framework' ),
					'icon'   => 'dashicons-admin-plugins',
					'url'    => admin_url( 'plugins.php?plugin_status=upgrade' ),
					'count'  => $updates['plugins'],
					'status' => $updates['plugins'] > 0
						? sprintf(
							/* translators: %d: Number of plugin updates. */
							_n(
								'%d update available',
								'%d updates available',
								$updates['plugins'],
								'goug-framework'
							),
							$updates['plugins']
						)
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['plugins'] > 0
						? 'warning'
						: 'success',
				),
				array(
					'title'  => __( 'Themes', 'goug-framework' ),
					'icon'   => 'dashicons-admin-appearance',
					'url'    => admin_url( 'update-core.php' ),
					'count'  => $updates['themes'],
					'status' => $updates['themes'] > 0
						? sprintf(
							/* translators: %d: Number of theme updates. */
							_n(
								'%d update available',
								'%d updates available',
								$updates['themes'],
								'goug-framework'
							),
							$updates['themes']
						)
						: __( 'Up to date', 'goug-framework' ),
					'state'  => $updates['themes'] > 0
						? 'warning'
						: 'success',
				),
			),
		);

		/*
		* Translation updates are normally handled automatically and are not
		* prominent enough to occupy a permanent dashboard card. Display the
		* card only when translation updates are actually pending.
		*/
		if ( $updates['translations'] > 0 ) {
			$system_updates['items'][] = array(
				'title'  => __( 'Translations', 'goug-framework' ),
				'icon'   => 'dashicons-translation',
				'url'    => admin_url( 'update-core.php' ),
				'count'  => $updates['translations'],
				'status' => sprintf(
					/* translators: %d: Number of translation updates. */
					_n(
						'%d update available',
						'%d updates available',
						$updates['translations'],
						'goug-framework'
					),
					$updates['translations']
				),
				'state'  => 'warning',
			);
		}

		/**
		 * Filter the System Updates section.
		 *
		 * @param array $system_updates Prepared System Updates data.
		 */
		return apply_filters(
			'goug_dashboard_system_updates',
			$system_updates
		);
	}

}