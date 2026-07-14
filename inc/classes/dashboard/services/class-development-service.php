<?php
/**
 * Dashboard development information service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides development environment information and shortcuts.
 */
class Development_Service {

	/**
	 * Request-level data cache.
	 *
	 * @var array|null
	 */
	private $development_data = null;

	/**
	 * Return development environment information.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->development_data ) {
			return $this->development_data;
		}

		$theme        = wp_get_theme();
		$parent_theme = $theme->parent();

		$is_child_theme = false !== $parent_theme;

		$environment = function_exists( 'wp_get_environment_type' )
			? wp_get_environment_type()
			: 'production';

		$debug_enabled = defined( 'WP_DEBUG' )
			&& WP_DEBUG;

		$debug_log_enabled = defined( 'WP_DEBUG_LOG' )
			&& WP_DEBUG_LOG;

		$debug_display_enabled = defined( 'WP_DEBUG_DISPLAY' )
			&& WP_DEBUG_DISPLAY;

		$script_debug_enabled = defined( 'SCRIPT_DEBUG' )
			&& SCRIPT_DEBUG;

		$debug_log_path = $this->get_debug_log_path();

		$this->development_data = array(
			'environment' => array(
				'type'        => $environment,
				'label'       => $this->format_environment(
					$environment
				),
				'is_local'    => in_array(
					$environment,
					array( 'local', 'development' ),
					true
				),
			),

			'theme' => array(
				'name'           => (string) $theme->get( 'Name' ),
				'version'        => (string) $theme->get( 'Version' ),
				'stylesheet'     => (string) $theme->get_stylesheet(),
				'template'       => (string) $theme->get_template(),
				'is_child_theme' => $is_child_theme,
				'parent_name'    => $is_child_theme
					? (string) $parent_theme->get( 'Name' )
					: '',
				'parent_version' => $is_child_theme
					? (string) $parent_theme->get( 'Version' )
					: '',
			),

			'runtime' => array(
				'php_version'       => PHP_VERSION,
				'wordpress_version' => get_bloginfo( 'version' ),
				'framework_version' => $this->get_framework_version(
					$theme
				),
			),

			'debug' => array(
				'enabled'         => $debug_enabled,
				'log_enabled'     => $debug_log_enabled,
				'display_enabled' => $debug_display_enabled,
				'script_debug'    => $script_debug_enabled,
				'log_exists'      => $debug_log_path
					? file_exists( $debug_log_path )
					: false,
				'log_readable'    => $debug_log_path
					? is_readable( $debug_log_path )
					: false,
			),

			'actions' => $this->get_actions(
				$environment
			),
		);

		/**
		 * Filter dashboard development information.
		 *
		 * @param array $development_data Development data.
		 */
		$this->development_data = apply_filters(
			'goug_dashboard_development_data',
			$this->development_data
		);

		return is_array( $this->development_data )
			? $this->development_data
			: array();
	}

	/**
	 * Return configured development actions.
	 *
	 * @param string $environment WordPress environment type.
	 *
	 * @return array
	 */
	private function get_actions( $environment ) {

		$actions = array(
			array(
				'id'          => 'site-health',
				'label'       => __(
					'Site Information',
					'goug-framework'
				),
				'description' => __(
					'View WordPress environment details',
					'goug-framework'
				),
				'icon'        => 'dashicons-info-outline',
				'url'         => admin_url(
					'site-health.php?tab=debug'
				),
				'protocols'   => array( 'http', 'https' ),
				'external'    => false,
				'capability'  => 'view_site_health_checks',
			),
			array(
				'id'          => 'theme-editor',
				'label'       => __(
					'Theme Editor',
					'goug-framework'
				),
				'description' => __(
					'Open the WordPress theme file editor',
					'goug-framework'
				),
				'icon'        => 'dashicons-editor-code',
				'url'         => admin_url( 'theme-editor.php' ),
				'protocols'   => array( 'http', 'https' ),
				'external'    => false,
				'capability'  => 'edit_themes',
				'visible'     => ! (
					defined( 'DISALLOW_FILE_EDIT' )
					&& DISALLOW_FILE_EDIT
				),
			),
			array(
				'id'          => 'plugins',
				'label'       => __(
					'Plugins',
					'goug-framework'
				),
				'description' => __(
					'Manage installed plugins',
					'goug-framework'
				),
				'icon'        => 'dashicons-admin-plugins',
				'url'         => admin_url( 'plugins.php' ),
				'protocols'   => array( 'http', 'https' ),
				'external'    => false,
				'capability'  => 'activate_plugins',
			),
		);

		$github_url = defined( 'GOUG_GITHUB_REPOSITORY_URL' )
			? trim( (string) GOUG_GITHUB_REPOSITORY_URL )
			: '';

		if ( '' !== $github_url ) {
			$actions[] = array(
				'id'          => 'github',
				'label'       => __(
					'GitHub Repository',
					'goug-framework'
				),
				'description' => __(
					'Open the project repository',
					'goug-framework'
				),
				'icon_svg'    => 'github.svg',
				'url'         => $github_url,
				'protocols'   => array( 'http', 'https' ),
				'external'    => true,
				'capability'  => 'manage_options',
			);
		}

		$vscode_url = defined( 'GOUG_VSCODE_URL' )
			? trim( (string) GOUG_VSCODE_URL )
			: '';

		if (
			'' !== $vscode_url
			&& in_array(
				$environment,
				array( 'local', 'development' ),
				true
			)
		) {
			$actions[] = array(
				'id'          => 'vscode',
				'label'       => __(
					'Visual Studio Code',
					'goug-framework'
				),
				'description' => __(
					'Open the project workspace',
					'goug-framework'
				),
				'icon'        => 'dashicons-editor-code',
				'url'         => $vscode_url,
				'protocols'   => array( 'vscode' ),
				'external'    => false,
				'capability'  => 'manage_options',
			);
		}

		/**
		 * Filter dashboard development actions.
		 *
		 * @param array  $actions     Development actions.
		 * @param string $environment WordPress environment.
		 */
		$actions = apply_filters(
			'goug_dashboard_development_actions',
			$actions,
			$environment
		);

		return $this->filter_actions( $actions );
	}

	/**
	 * Remove inaccessible or invalid actions.
	 *
	 * @param array $actions Development actions.
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
						|| empty( $action['label'] )
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
						? $action['capability']
						: 'read';

					return current_user_can( $capability );
				}
			)
		);
	}

	/**
	 * Return the WordPress debug log path.
	 *
	 * @return string
	 */
	private function get_debug_log_path() {

		if (
			! defined( 'WP_DEBUG_LOG' )
			|| ! WP_DEBUG_LOG
		) {
			return '';
		}

		if ( is_string( WP_DEBUG_LOG ) ) {
			return WP_DEBUG_LOG;
		}

		return WP_CONTENT_DIR . '/debug.log';
	}

	/**
	 * Return the framework version.
	 *
	 * @param \WP_Theme $theme Active theme.
	 *
	 * @return string
	 */
	private function get_framework_version( $theme ) {

		if ( defined( 'GOUG_VERSION' ) ) {
			return (string) GOUG_VERSION;
		}

		return (string) $theme->get( 'Version' );
	}

	/**
	 * Format an environment identifier.
	 *
	 * @param string $environment Environment identifier.
	 *
	 * @return string
	 */
	private function format_environment( $environment ) {

		return ucwords(
			str_replace(
				array( '-', '_' ),
				' ',
				$environment
			)
		);
	}
}