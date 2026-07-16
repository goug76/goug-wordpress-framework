<?php
/**
 * Dashboard setting definitions.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Settings\Definitions;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Settings\Settings_Registry;

/**
 * Registers dashboard-related framework settings.
 */
class Dashboard_Settings {

	/**
	 * Register dashboard setting definitions.
	 *
	 * @param Settings_Registry $registry Framework settings registry.
	 *
	 * @return void
	 */
	public function register( Settings_Registry $registry ) {

		$registry->register_setting(
			array(
				'id'          => 'dashboard.hidden_panels',
				'section'     => 'dashboard',
				'label'       => __(
					'Hidden Panels',
					'goug-framework'
				),
				'description' => __(
					'Panels hidden from the current user’s dashboard.',
					'goug-framework'
				),
				'type'        => 'multiselect',
				'scope'       => 'user',
				'default'     => array(),
				'priority'    => 10,
				'capability'  => 'read',
			)
		);

		$registry->register_setting(
			array(
				'id'          => 'dashboard.panel_order',
				'section'     => 'dashboard',
				'label'       => __(
					'Panel Layout',
					'goug-framework'
				),
				'description' => __(
					'Saved dashboard panel positions and widths.',
					'goug-framework'
				),
				'type'        => 'text',
				'sanitize_callback' => array(
					self::class,
					'sanitize_panel_order',
				),
				'scope'       => 'user',
				'default'     => array(),
				'priority'    => 20,
				'capability'  => 'read',
				'visible'     => false,
			)
		);

		$registry->register_setting(
			array(
				'id'          => 'dashboard.collapsed_panels',
				'section'     => 'dashboard',
				'label'       => __(
					'Collapsed Panels',
					'goug-framework'
				),
				'description' => __(
					'Panels collapsed by the current user.',
					'goug-framework'
				),
				'type'        => 'multiselect',
				'scope'       => 'user',
				'default'     => array(),
				'priority'    => 30,
				'capability'  => 'read',
				'visible'     => false,
			)
		);

		$registry->register_setting(
			array(
				'id'          => 'dashboard.density',
				'section'     => 'dashboard',
				'label'       => __(
					'Dashboard Density',
					'goug-framework'
				),
				'description' => __(
					'Controls the spacing used throughout the dashboard.',
					'goug-framework'
				),
				'type'        => 'select',
				'scope'       => 'user',
				'default'     => 'comfortable',
				'choices'     => array(
					'compact' => __(
						'Compact',
						'goug-framework'
					),
					'comfortable' => __(
						'Comfortable',
						'goug-framework'
					),
					'spacious' => __(
						'Spacious',
						'goug-framework'
					),
				),
				'priority'   => 40,
				'capability' => 'read',
			)
		);

		$registry->register_setting(
			array(
				'id'          => 'dashboard.show_greeting',
				'section'     => 'dashboard',
				'label'       => __(
					'Show Greeting',
					'goug-framework'
				),
				'description' => __(
					'Displays the personalized greeting in the dashboard header.',
					'goug-framework'
				),
				'type'        => 'checkbox',
				'scope'       => 'user',
				'default'     => true,
				'priority'    => 50,
				'capability'  => 'read',
			)
		);

		$registry->register_setting(
			array(
				'id'          => 'dashboard.enable_motion',
				'section'     => 'dashboard',
				'label'       => __(
					'Dashboard Animations',
					'goug-framework'
				),
				'description' => __(
					'Enables dashboard transitions and motion effects.',
					'goug-framework'
				),
				'type'        => 'checkbox',
				'scope'       => 'user',
				'default'     => true,
				'priority'    => 60,
				'capability'  => 'read',
			)
		);
	}

	/**
	 * Sanitize saved dashboard panel layout data.
	 *
	 * @param mixed $value Raw panel layout collection.
	 *
	 * @return array
	 */
	public static function sanitize_panel_order(
		$value ) {

		if ( ! is_array( $value ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( $value as $panel_id => $layout ) {

			$panel_id = sanitize_key(
				$panel_id
			);

			if (
				'' === $panel_id ||
				! is_array( $layout )
			) {
				continue;
			}

			$width = isset( $layout['width'] )
				? sanitize_key( $layout['width'] )
				: 'full';

			$allowed_widths = array(
				'full',
				'half',
				'third',
				'quarter',
			);

			if ( ! in_array( $width, $allowed_widths, true ) ) {
				$width = 'full';
			}

			$sanitized[ $panel_id ] = array(
				'row' => isset( $layout['row'] )
					? max( 1, (int) $layout['row'] )
					: 1,

				'priority' => isset( $layout['priority'] )
					? max( 0, (int) $layout['priority'] )
					: 100,

				'width' => $width,
			);
		}

		return $sanitized;
	}
}