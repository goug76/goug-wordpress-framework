<?php
/**
 * Dashboard Quick Actions panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;

/**
 * Registers and prepares the Quick Actions dashboard panel.
 */
class Panel_Quick_Actions implements Dashboard_Panel {

	/**
	 * Register the Quick Actions panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$actions = $this->get_data();

		$groups = array_filter(
			array(
				$actions['essential'] ?? array(),
				$actions['settings'] ?? array(),
				$actions['design'] ?? array(),
			)
		);

		if ( empty( $groups ) ) {
			return;
		}

		$registry->register_panel(
			array(
				'id'         => 'quick-actions',
				'title'      => __( 'Quick Actions', 'goug-framework' ),
				'icon'       => 'dashicons-lightning',
				'priority'   => 20,
				'class_name' => 'goug-panel--quick-actions',
				'body_view'  => 'dashboard/components/quick-actions',
				'body_data'  => array(
					'groups' => $groups,
				),
				'capability' => 'manage_options',
				'attributes' => array(
					'data-panel-id' => 'quick-actions',
				),
			)
		);
	}

	/**
	 * Return dashboard action groups.
	 *
	 * @return array
	 */
	private function get_data() {

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

		$actions = apply_filters(
			'goug_dashboard_actions',
			$actions
		);

		return $this->filter_actions( $actions );
	}

	/**
	 * Remove invalid, hidden, and inaccessible actions.
	 *
	 * @param array $groups Action groups.
	 *
	 * @return array
	 */
	private function filter_actions( $groups ) {

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
}