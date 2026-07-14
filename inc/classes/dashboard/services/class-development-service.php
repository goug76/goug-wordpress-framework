<?php
/**
 * Dashboard development information service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides development environment information for the dashboard.
 *
 * Responsibilities:
 *
 * - Detect the current WordPress environment.
 * - Collect active theme and runtime information.
 * - Inspect WordPress debugging configuration.
 * - Build normalized facts for dashboard presentation.
 * - Build capability-aware development shortcuts.
 *
 * This service returns prepared data only. It does not render HTML.
 */
class Development_Service {

	/**
	 * Request-level data cache.
	 *
	 * Prevents the service from rebuilding the same information more
	 * than once during a single WordPress request.
	 *
	 * @var array|null
	 */
	private $development_data = null;

	/**
	 * Return normalized development information.
	 *
	 * The legacy environment, theme, runtime, and debug arrays are
	 * temporarily preserved while the Development template is migrated
	 * to the new summary and facts structures.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->development_data ) {
			return $this->development_data;
		}

		$environment = $this->get_environment_data();
		$theme       = $this->get_theme_data();
		$runtime     = $this->get_runtime_data( $theme );
		$debug       = $this->get_debug_data();

		$this->development_data = array(
			/*
			 * Normalized presentation data.
			 */
			'summary' => $this->get_summary(
				$environment
			),
			'facts'   => $this->get_facts(
				$theme,
				$runtime,
				$debug
			),
			'actions' => $this->get_actions(
				$environment['type']
			),

			/*
			 * Temporary backwards-compatible data.
			 *
			 * Remove these after the Development template has been
			 * converted to use summary and facts.
			 */
			'environment' => $environment,
			'theme'       => $theme,
			'runtime'     => $runtime,
			'debug'       => $debug,
		);

		/**
		 * Filter dashboard development information.
		 *
		 * @param array $development_data Development information.
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
	 * Return the normalized environment summary.
	 *
	 * This structure contains only the information required by the
	 * environment banner in the Development panel.
	 *
	 * @param array $environment Environment information.
	 *
	 * @return array
	 */
	private function get_summary( $environment ) {

		$is_local = ! empty( $environment['is_local'] );

		return array(
			'label' => isset( $environment['label'] )
				? (string) $environment['label']
				: __( 'Production', 'goug-framework' ),
			'state' => $is_local
				? 'development'
				: 'production',
			'icon'  => 'dashicons-admin-site-alt3',
		);
	}

	/**
	 * Build normalized Development panel facts.
	 *
	 * Converts theme, runtime, and debug information into a consistent
	 * presentation-friendly structure. The template can render every
	 * fact through a single loop instead of containing six hardcoded
	 * markup blocks.
	 *
	 * @param array $theme   Active theme information.
	 * @param array $runtime WordPress and PHP runtime information.
	 * @param array $debug   WordPress debugging information.
	 *
	 * @return array
	 */
	private function get_facts(
		$theme,
		$runtime,
		$debug
	) {

		$is_child_theme = ! empty(
			$theme['is_child_theme']
		);

		$parent_description = '';

		if (
			$is_child_theme &&
			! empty( $theme['parent_name'] )
		) {
			$parent_description = sprintf(
				/* translators: %s: Parent theme name. */
				__(
					'Parent: %s',
					'goug-framework'
				),
				$theme['parent_name']
			);
		}

		$facts = array(
			array(
				'id'          => 'active-theme',
				'label'       => __(
					'Active Theme',
					'goug-framework'
				),
				'value'       => isset( $theme['name'] )
					? (string) $theme['name']
					: '',
				'description' => sprintf(
					/* translators: %s: Theme version. */
					__(
						'Version %s',
						'goug-framework'
					),
					isset( $theme['version'] )
						? $theme['version']
						: ''
				),
				'state'       => 'default',
			),
			array(
				'id'          => 'theme-type',
				'label'       => __(
					'Theme Type',
					'goug-framework'
				),
				'value'       => $is_child_theme
					? __( 'Child Theme', 'goug-framework' )
					: __( 'Parent Theme', 'goug-framework' ),
				'description' => $parent_description,
				'state'       => 'default',
			),
			array(
				'id'          => 'framework',
				'label'       => __(
					'Framework',
					'goug-framework'
				),
				'value'       => isset(
					$runtime['framework_version']
				)
					? (string) $runtime['framework_version']
					: '',
				'description' => sprintf(
					/* translators: %s: WordPress version. */
					__(
						'WordPress %s',
						'goug-framework'
					),
					isset( $runtime['wordpress_version'] )
						? $runtime['wordpress_version']
						: ''
				),
				'state'       => 'default',
			),
			array(
				'id'          => 'php',
				'label'       => __(
					'PHP',
					'goug-framework'
				),
				'value'       => isset(
					$runtime['php_version']
				)
					? (string) $runtime['php_version']
					: '',
				'description' => ! empty(
					$debug['script_debug']
				)
					? __(
						'Unminified assets',
						'goug-framework'
					)
					: __(
						'Production assets',
						'goug-framework'
					),
				'state'       => 'default',
			),
			array(
				'id'          => 'debug-mode',
				'label'       => __(
					'Debug Mode',
					'goug-framework'
				),
				'value'       => ! empty( $debug['enabled'] )
					? __( 'Enabled', 'goug-framework' )
					: __( 'Disabled', 'goug-framework' ),
				'description' => ! empty(
					$debug['log_enabled']
				)
					? __(
						'Logging enabled',
						'goug-framework'
					)
					: __(
						'Logging disabled',
						'goug-framework'
					),
				'state'       => ! empty( $debug['enabled'] )
					? 'enabled'
					: 'disabled',
			),
			array(
				'id'          => 'debug-log',
				'label'       => __(
					'Debug Log',
					'goug-framework'
				),
				'value'       => ! empty(
					$debug['log_exists']
				)
					? __( 'Available', 'goug-framework' )
					: __( 'Not Found', 'goug-framework' ),
				'description' => ! empty(
					$debug['log_readable']
				)
					? __( 'Readable', 'goug-framework' )
					: __( 'Unavailable', 'goug-framework' ),
				'state'       => ! empty(
					$debug['log_readable']
				)
					? 'success'
					: 'default',
			),
		);

		/**
		 * Filter Development panel facts.
		 *
		 * @param array $facts   Prepared development facts.
		 * @param array $theme   Active theme information.
		 * @param array $runtime Runtime information.
		 * @param array $debug   Debugging information.
		 */
		$facts = apply_filters(
			'goug_dashboard_development_facts',
			$facts,
			$theme,
			$runtime,
			$debug
		);

		return is_array( $facts )
			? array_values( $facts )
			: array();
	}

	/**
	 * Return normalized WordPress environment information.
	 *
	 * @return array
	 */
	private function get_environment_data() {

		$environment = function_exists(
			'wp_get_environment_type'
		)
			? wp_get_environment_type()
			: 'production';

		return array(
			'type'     => $environment,
			'label'    => $this->format_environment(
				$environment
			),
			'is_local' => in_array(
				$environment,
				array( 'local', 'development' ),
				true
			),
		);
	}

	/**
	 * Return active and parent theme information.
	 *
	 * WordPress theme APIs are used instead of assuming the framework
	 * is always installed as either a parent or child theme.
	 *
	 * @return array
	 */
	private function get_theme_data() {

		$theme        = wp_get_theme();
		$parent_theme = $theme->parent();

		$is_child_theme = false !== $parent_theme;

		return array(
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
		);
	}

	/**
	 * Return WordPress, framework, and PHP runtime information.
	 *
	 * @param array $theme Active theme information.
	 *
	 * @return array
	 */
	private function get_runtime_data( $theme ) {

		return array(
			'php_version'       => PHP_VERSION,
			'wordpress_version' => get_bloginfo( 'version' ),
			'framework_version' => $this->get_framework_version(
				$theme
			),
		);
	}

	/**
	 * Return the current WordPress debugging configuration.
	 *
	 * The debug-log path itself is not exposed to the dashboard. Only
	 * safe status information is returned.
	 *
	 * @return array
	 */
	private function get_debug_data() {

		$debug_enabled = defined( 'WP_DEBUG' )
			&& WP_DEBUG;

		$debug_log_enabled = defined( 'WP_DEBUG_LOG' )
			&& WP_DEBUG_LOG;

		$debug_display_enabled = defined( 'WP_DEBUG_DISPLAY' )
			&& WP_DEBUG_DISPLAY;

		$script_debug_enabled = defined( 'SCRIPT_DEBUG' )
			&& SCRIPT_DEBUG;

		$debug_log_path = $this->get_debug_log_path();

		return array(
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
		);
	}

	/**
	 * Return configured development actions.
	 *
	 * Actions are filtered by visibility and user capability before
	 * being returned to the dashboard template.
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

		$github_url = defined(
			'GOUG_GITHUB_REPOSITORY_URL'
		)
			? trim(
				(string) GOUG_GITHUB_REPOSITORY_URL
			)
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
	 * Remove invalid or inaccessible development actions.
	 *
	 * An action must contain a label and URL, be explicitly visible,
	 * and pass its configured WordPress capability check.
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
	 * Return the configured WordPress debug-log path.
	 *
	 * WordPress accepts either a Boolean value or a custom filesystem
	 * path for WP_DEBUG_LOG.
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
	 * Return the current GOUG Framework version.
	 *
	 * The framework constant takes precedence. The active theme version
	 * remains a safe fallback while the project can operate as a theme.
	 *
	 * @param array $theme Active theme information.
	 *
	 * @return string
	 */
	private function get_framework_version( $theme ) {

		if ( defined( 'GOUG_VERSION' ) ) {
			return (string) GOUG_VERSION;
		}

		return isset( $theme['version'] )
			? (string) $theme['version']
			: '';
	}

	/**
	 * Convert an environment identifier into a readable label.
	 *
	 * Examples:
	 *
	 * - development becomes Development.
	 * - local-development becomes Local Development.
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