<?php
/**
 * Dashboard Quick Actions service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides grouped administrative shortcuts for the dashboard.
 *
 * Responsibilities:
 *
 * - Define the default Quick Actions groups.
 * - Add actions that depend on available WordPress features.
 * - Allow third parties to modify registered actions.
 * - Remove invalid, hidden, and inaccessible actions.
 * - Return normalized group data for dashboard presentation.
 *
 * This service returns prepared data only. It does not register
 * dashboard panels or render HTML.
 */
class Quick_Actions_Service {

	/**
	 * Request-level action cache.
	 *
	 * Prevents the action groups from being rebuilt more than once
	 * during a single WordPress request.
	 *
	 * @var array|null
	 */
	private $action_groups = null;

	/**
	 * Return normalized Quick Actions groups.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->action_groups ) {
			return $this->action_groups;
		}

		$groups = array(
			$this->get_essential_group(),
			$this->get_settings_group(),
			$this->get_design_group(),
		);

		/**
		 * Filter dashboard Quick Actions groups.
		 *
		 * Each group should contain:
		 *
		 * - id
		 * - title
		 * - items
		 *
		 * @param array $groups Quick Actions groups.
		 */
		$groups = apply_filters(
			'goug_dashboard_actions',
			$groups
		);

		$this->action_groups = $this->filter_groups(
			$groups
		);

		return $this->action_groups;
	}

	/**
	 * Build the Essential Actions group.
	 *
	 * These actions provide shortcuts to the primary content and
	 * extension-management areas used by site administrators.
	 *
	 * @return array
	 */
	private function get_essential_group() {

		$items = array(
			array(
				'id'          => 'posts',
				'title'       => __(
					'Posts',
					'goug-framework'
				),
				'icon'        => 'dashicons-admin-post',
				'url'         => admin_url( 'edit.php' ),
				'capability'  => 'edit_posts',
				'description' => __(
					'View and manage posts',
					'goug-framework'
				),
			),
			array(
				'id'          => 'pages',
				'title'       => __(
					'Pages',
					'goug-framework'
				),
				'icon'        => 'dashicons-admin-page',
				'url'         => admin_url(
					'edit.php?post_type=page'
				),
				'capability'  => 'edit_pages',
				'description' => __(
					'View and manage pages',
					'goug-framework'
				),
			),
		);

		/*
		 * Courses are optional and should appear only when the
		 * corresponding post type has been registered.
		 */
		if ( post_type_exists( 'courses' ) ) {
			$items[] = array(
				'id'          => 'courses',
				'title'       => __(
					'Courses',
					'goug-framework'
				),
				'icon'        => 'dashicons-welcome-learn-more',
				'url'         => admin_url(
					'admin.php?page=tutor'
				),
				'capability'  => 'edit_posts',
				'description' => __(
					'View and manage courses',
					'goug-framework'
				),
			);
		}

		$items[] = array(
			'id'          => 'plugins',
			'title'       => __(
				'Plugins',
				'goug-framework'
			),
			'icon'        => 'dashicons-admin-plugins',
			'url'         => admin_url( 'plugins.php' ),
			'capability'  => 'activate_plugins',
			'description' => __(
				'View and manage plugins',
				'goug-framework'
			),
		);

		$items[] = array(
			'id'          => 'theme',
			'title'       => __(
				'Theme',
				'goug-framework'
			),
			'icon'        => 'dashicons-admin-appearance',
			'url'         => admin_url( 'themes.php' ),
			'capability'  => 'switch_themes',
			'description' => __(
				'View and manage themes',
				'goug-framework'
			),
		);

		return array(
			'id'    => 'essential',
			'title' => __(
				'Essential Actions',
				'goug-framework'
			),
			'items' => $items,
		);
	}

	/**
	 * Build the Site Settings Actions group.
	 *
	 * These actions provide shortcuts to frequently used WordPress
	 * administration and configuration screens.
	 *
	 * @return array
	 */
	private function get_settings_group() {

		return array(
			'id'    => 'settings',
			'title' => __(
				'Site Settings Actions',
				'goug-framework'
			),
			'items' => array(
				array(
					'id'          => 'updates',
					'title'       => __(
						'Updates',
						'goug-framework'
					),
					'icon'        => 'dashicons-update',
					'url'         => admin_url(
						'update-core.php'
					),
					'capability'  => 'update_core',
					'description' => __(
						'Manage WordPress updates',
						'goug-framework'
					),
				),
				array(
					'id'          => 'users',
					'title'       => __(
						'Users',
						'goug-framework'
					),
					'icon'        => 'dashicons-admin-users',
					'url'         => admin_url( 'users.php' ),
					'capability'  => 'list_users',
					'description' => __(
						'View and manage users',
						'goug-framework'
					),
				),
				array(
					'id'          => 'permalinks',
					'title'       => __(
						'Permalinks',
						'goug-framework'
					),
					'icon'        => 'dashicons-admin-links',
					'url'         => admin_url(
						'options-permalink.php'
					),
					'capability'  => 'manage_options',
					'description' => __(
						'Manage permalink settings',
						'goug-framework'
					),
				),
				array(
					'id'          => 'health',
					'title'       => __(
						'Health',
						'goug-framework'
					),
					'icon'        => 'dashicons-heart',
					'url'         => admin_url(
						'site-health.php'
					),
					'capability'  => 'manage_options',
					'description' => __(
						'View Site Health information',
						'goug-framework'
					),
				),
			),
		);
	}

	/**
	 * Build the Site Design Actions group.
	 *
	 * Theme Editor is included only when WordPress file editing has
	 * not been disabled through configuration.
	 *
	 * @return array
	 */
	private function get_design_group() {

		return array(
			'id'    => 'design',
			'title' => __(
				'Site Design Actions',
				'goug-framework'
			),
			'items' => array(
				array(
					'id'          => 'menus',
					'title'       => __(
						'Menus',
						'goug-framework'
					),
					'icon'        => 'dashicons-menu',
					'url'         => admin_url(
						'nav-menus.php'
					),
					'capability'  => 'edit_theme_options',
					'description' => __(
						'Manage navigation menus',
						'goug-framework'
					),
				),
				array(
					'id'          => 'widgets',
					'title'       => __(
						'Widgets',
						'goug-framework'
					),
					'icon'        => 'dashicons-admin-generic',
					'url'         => admin_url(
						'widgets.php'
					),
					'capability'  => 'edit_theme_options',
					'description' => __(
						'Manage widget areas',
						'goug-framework'
					),
				),
				array(
					'id'          => 'customize',
					'title'       => __(
						'Customize',
						'goug-framework'
					),
					'icon'        => 'dashicons-admin-customizer',
					'url'         => admin_url(
						'customize.php'
					),
					'capability'  => 'edit_theme_options',
					'description' => __(
						'Open the Theme Customizer',
						'goug-framework'
					),
				),
				array(
					'id'          => 'editor',
					'title'       => __(
						'Editor',
						'goug-framework'
					),
					'icon'        => 'dashicons-editor-code',
					'url'         => admin_url(
						'theme-editor.php'
					),
					'capability'  => 'edit_themes',
					'description' => __(
						'Open the Theme File Editor',
						'goug-framework'
					),
					'visible'     => ! (
						defined( 'DISALLOW_FILE_EDIT' )
						&& DISALLOW_FILE_EDIT
					),
				),
			),
		);
	}

	/**
	 * Remove invalid, empty, or inaccessible action groups.
	 *
	 * Each group is normalized before being returned. Empty groups are
	 * removed after their unavailable actions have been filtered out.
	 *
	 * @param array $groups Quick Actions groups.
	 *
	 * @return array
	 */
	private function filter_groups( $groups ) {

		if ( ! is_array( $groups ) ) {
			return array();
		}

		$filtered_groups = array();

		foreach ( $groups as $group ) {

			if (
				! is_array( $group )
				|| empty( $group['items'] )
				|| ! is_array( $group['items'] )
			) {
				continue;
			}

			$items = $this->filter_actions(
				$group['items']
			);

			if ( empty( $items ) ) {
				continue;
			}

			$filtered_groups[] = array(
				'id'    => isset( $group['id'] )
					? sanitize_key( $group['id'] )
					: '',
				'title' => isset( $group['title'] )
					? (string) $group['title']
					: '',
				'items' => $items,
			);
		}

		return $filtered_groups;
	}

	/**
	 * Remove invalid, hidden, or inaccessible actions.
	 *
	 * Actions must contain a title and URL, pass their visibility
	 * check, and be available to the current WordPress user.
	 *
	 * @param array $actions Quick Action definitions.
	 *
	 * @return array
	 */
	private function filter_actions( $actions ) {

		if ( ! is_array( $actions ) ) {
			return array();
		}

		return array_values(
			array_filter(
				$actions,
				static function ( $action ) {

					if (
						! is_array( $action )
						|| empty( $action['title'] )
						|| empty( $action['url'] )
					) {
						return false;
					}

					if (
						isset( $action['visible'] )
						&& false === (bool) $action['visible']
					) {
						return false;
					}

					$capability = ! empty(
						$action['capability']
					)
						? (string) $action['capability']
						: 'read';

					return current_user_can( $capability );
				}
			)
		);
	}
}