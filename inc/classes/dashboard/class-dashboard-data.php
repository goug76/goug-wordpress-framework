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

/**
 * Provides data for the custom admin dashboard.
 *
 * This class is intentionally not a singleton. It does not register
 * WordPress hooks or maintain global state; it simply gathers data
 * when the dashboard coordinator requests it.
 */
class Dashboard_Data {

	/**
	 * Cached content counts for the current request.
	 *
	 * @var array|null
	 */
	private $content_counts = null;

	/**
	 * Cached WordPress update data for the current request.
	 *
	 * @var array|null
	 */
	private $update_data = null;

	/**
	 * Return all currently supported dashboard data.
	 *
	 * @return array
	 */
	public function get_data() {

		return array(
			'site'           => $this->get_site_data(),
			'user' => array(
					'display_name' => wp_get_current_user()->display_name,
				),
			'counts'         => $this->get_content_counts(),
			'updates'        => $this->get_update_data(),
			'system'         => $this->get_system_data(),
			'actions'        => $this->get_action_data(),
			'overview'       => $this->get_overview_data(),
			'system_updates' => $this->get_system_updates_data(),
		);
	}

	/**
	 * Return basic site information.
	 *
	 * @return array
	 */
	private function get_site_data() {

		$site_name = get_bloginfo( 'name' );

		return array(
			'name'        => $site_name
				? $site_name
				: __( 'WordPress', 'goug-framework' ),
			'description' => get_bloginfo( 'description' ),
			'url'         => home_url( '/' ),
			'admin_url'   => admin_url(),
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
	 * Return available WordPress update counts.
	 *
	 * WordPress stores update information in transients, so this does not
	 * perform a remote request whenever the dashboard is loaded.
	 *
	 * @return array
	 */
	private function get_update_data() {

		if ( null !== $this->update_data ) {
			return $this->update_data;
		}

		$update_data = wp_get_update_data();
		$counts      = isset( $update_data['counts'] )
			&& is_array( $update_data['counts'] )
				? $update_data['counts']
				: array();

		$this->update_data = array(
			'core' => isset( $counts['wordpress'] )
				? (int) $counts['wordpress']
				: 0,
			'plugins' => isset( $counts['plugins'] )
				? (int) $counts['plugins']
				: 0,
			'themes' => isset( $counts['themes'] )
				? (int) $counts['themes']
				: 0,
			'translations' => isset( $counts['translations'] )
				? (int) $counts['translations']
				: 0,
			'total' => isset( $counts['total'] )
				? (int) $counts['total']
				: 0,
		);

		return $this->update_data;
	}

	/**
	 * Return lightweight system information.
	 *
	 * @return array
	 */
	private function get_system_data() {

		global $wp_version;

		$theme = wp_get_theme();

		return array(
			'wordpress_version' => (string) $wp_version,
			'php_version'       => PHP_VERSION,
			'theme_name'        => $theme->get( 'Name' ),
			'theme_version'     => $theme->get( 'Version' ),
			'is_https'          => is_ssl(),
			'debug_enabled'     => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'environment'       => function_exists( 'wp_get_environment_type' )
				? wp_get_environment_type()
				: 'production',
		);
	}

	/**
	 * Return dashboard action groups.
	 *
	 * Each action follows a common schema so the dashboard card component
	 * can render it consistently.
	 *
	 * @return array
	 */
	private function get_action_data() {

		$essential_actions = array(
			array(
				'title'       => __( 'Posts', 'goug-framework' ),
				'icon'        => 'dashicons-admin-post',
				'url'         => admin_url( 'edit.php' ),
				'capability'  => 'edit_posts',
				'description' => __( 'View and manage posts', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Pages', 'goug-framework' ),
				'icon'        => 'dashicons-admin-page',
				'url'         => admin_url( 'edit.php?post_type=page' ),
				'capability'  => 'edit_pages',
				'description' => __( 'View and manage pages', 'goug-framework' ),
			),
		);

		/*
		* Add the Courses action only when its post type exists.
		*/
		if ( post_type_exists( 'courses' ) ) {
			$essential_actions[] = array(
				'title'       => __( 'Courses', 'goug-framework' ),
				'icon'        => 'dashicons-welcome-learn-more',
				'url'         => admin_url( 'admin.php?page=tutor' ),
				'capability'  => 'edit_posts',
				'description' => __( 'View and manage courses', 'goug-framework' ),
			);
		}

		$essential_actions = array_merge(
			$essential_actions,
			array(
				array(
					'title'       => __( 'Plugins', 'goug-framework' ),
					'icon'        => 'dashicons-admin-plugins',
					'url'         => admin_url( 'plugins.php' ),
					'capability'  => 'activate_plugins',
					'description' => __( 'View and manage plugins', 'goug-framework' ),
				),
				array(
					'title'       => __( 'Theme', 'goug-framework' ),
					'icon'        => 'dashicons-admin-appearance',
					'url'         => admin_url( 'themes.php' ),
					'capability'  => 'switch_themes',
					'description' => __( 'View and manage themes', 'goug-framework' ),
				),
				array(
					'title'       => __( 'Site', 'goug-framework' ),
					'icon'        => 'dashicons-admin-site',
					'url'         => home_url( '/' ),
					'capability'  => 'read',
					'description' => __( 'Visit the website', 'goug-framework' ),
					'target'      => '_blank',
				),
			)
		);

		$settings_actions = array(
			array(
				'title'       => __( 'Updates', 'goug-framework' ),
				'icon'        => 'dashicons-update',
				'url'         => admin_url( 'update-core.php' ),
				'capability'  => 'update_core',
				'description' => __( 'Manage WordPress updates', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Users', 'goug-framework' ),
				'icon'        => 'dashicons-admin-users',
				'url'         => admin_url( 'users.php' ),
				'capability'  => 'list_users',
				'description' => __( 'View and manage users', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Permalinks', 'goug-framework' ),
				'icon'        => 'dashicons-admin-links',
				'url'         => admin_url( 'options-permalink.php' ),
				'capability'  => 'manage_options',
				'description' => __( 'Manage permalink settings', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Health', 'goug-framework' ),
				'icon'        => 'dashicons-heart',
				'url'         => admin_url( 'site-health.php' ),
				'capability'  => 'manage_options',
				'description' => __( 'View Site Health information', 'goug-framework' ),
			),
		);

		$design_actions = array(
			array(
				'title'       => __( 'Menus', 'goug-framework' ),
				'icon'        => 'dashicons-menu',
				'url'         => admin_url( 'nav-menus.php' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Manage navigation menus', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Widgets', 'goug-framework' ),
				'icon'        => 'dashicons-admin-generic',
				'url'         => admin_url( 'widgets.php' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Manage widget areas', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Customize', 'goug-framework' ),
				'icon'        => 'dashicons-admin-customizer',
				'url'         => admin_url( 'customize.php' ),
				'capability'  => 'edit_theme_options',
				'description' => __( 'Open the Theme Customizer', 'goug-framework' ),
			),
			array(
				'title'       => __( 'Editor', 'goug-framework' ),
				'icon'        => 'dashicons-editor-code',
				'url'         => admin_url( 'theme-editor.php' ),
				'capability'  => 'edit_themes',
				'description' => __( 'Open the Theme File Editor', 'goug-framework' ),
				'visible'     => ! (
					defined( 'DISALLOW_FILE_EDIT' ) &&
					DISALLOW_FILE_EDIT
				),
			),
		);

		$actions = array(
			'essential' => array(
				'title' => __( 'Essential Actions', 'goug-framework' ),
				'items' => $essential_actions,
			),
			'settings' => array(
				'title' => __( 'Site Settings Actions', 'goug-framework' ),
				'items' => $settings_actions,
			),
			'design' => array(
				'title' => __( 'Site Design Actions', 'goug-framework' ),
				'items' => $design_actions,
			),
		);

		/**
		 * Filter all dashboard action groups.
		 *
		 * Developers can add, remove, or modify action cards without editing
		 * the framework templates.
		 *
		 * @param array $actions Dashboard action groups.
		 */
		$actions = apply_filters(
			'goug_dashboard_actions',
			$actions
		);

		return $this->filter_action_data( $actions );
	}

	/**
	 * Remove invalid, hidden, and inaccessible dashboard actions.
	 *
	 * @param array $groups Dashboard action groups.
	 *
	 * @return array
	 */
	private function filter_action_data( $groups ) {

		if ( ! is_array( $groups ) ) {
			return array();
		}

		foreach ( $groups as $group_key => $group ) {

			if (
				! is_array( $group ) ||
				empty( $group['items'] ) ||
				! is_array( $group['items'] )
			) {
				unset( $groups[ $group_key ] );
				continue;
			}

			$group['items'] = array_values(
				array_filter(
					$group['items'],
					static function ( $action ) {

						if (
							! is_array( $action ) ||
							empty( $action['title'] ) ||
							empty( $action['url'] )
						) {
							return false;
						}

						if (
							isset( $action['visible'] ) &&
							false === (bool) $action['visible']
						) {
							return false;
						}

						$capability = ! empty( $action['capability'] )
							? $action['capability']
							: 'read';

						return current_user_can( $capability );
					}
				)
			);

			if ( empty( $group['items'] ) ) {
				unset( $groups[ $group_key ] );
				continue;
			}

			$groups[ $group_key ] = $group;
		}

		return $groups;
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

		$updates = $this->get_update_data();

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