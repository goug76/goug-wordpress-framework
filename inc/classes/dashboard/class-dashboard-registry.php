<?php
/**
 * Dashboard panel registry.
 *
 * Stores and sorts dashboard panel definitions.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard;

defined( 'ABSPATH' ) || exit;

/**
 * Dashboard panel registry.
 *
 * Responsibilities:
 *
 * - Register dashboard panels.
 * - Normalize panel definitions.
 * - Validate panel configuration.
 * - Filter unavailable panels.
 * - Sort panels by row and priority.
 *
 * The registry stores metadata only.
 * It does not collect dashboard data or render HTML.
 */
class Dashboard_Registry {

	/**
	 * Registered dashboard panels.
	 *
	 * @var array
	 */
	private $panels = array();

	/**
     * Register a dashboard panel.
     *
     * Required:
     *
     * - id
     * - title
     * - body_view
     *
     * Optional:
     *
     * - icon
     * - icon_svg
     * - priority
     * - class_name
     * - body_data
     * - capability
     * - visible
     * - attributes
     *
     * @param array $panel Panel definition.
     *
     * @return bool
     */
	public function register_panel( array $panel ) {

		$panel = $this->normalize_panel( $panel );

		if ( empty( $panel ) ) {
			return false;
		}

		$this->panels[ $panel['id'] ] = $panel;

		return true;
	}

	/**
	 * Remove a registered panel.
	 *
	 * @param string $panel_id Panel ID.
	 *
	 * @return bool
	 */
	public function unregister_panel( $panel_id ) {

		$panel_id = sanitize_key( $panel_id );

		if (
			'' === $panel_id ||
			! isset( $this->panels[ $panel_id ] )
		) {
			return false;
		}

		unset( $this->panels[ $panel_id ] );

		return true;
	}

	/**
	 * Determine whether a panel is registered.
	 *
	 * @param string $panel_id Panel ID.
	 *
	 * @return bool
	 */
	public function has_panel( $panel_id ) {

		return isset(
			$this->panels[ sanitize_key( $panel_id ) ]
		);
	}

	/**
	 * Return all visible, accessible panels.
	 *
	 * Panels are filtered by capability, adjusted for the current user's
	 * default layout profile, and sorted by row, priority, and ID.
	 *
	 * @return array
	 */
	public function get_panels() {

		$panels = array_filter(
			$this->panels,
			array( $this, 'is_panel_available' )
		);

		$panels = $this->apply_layout_profile(
			$panels
		);

		uasort(
			$panels,
			static function ( $first, $second ) {

				$first_row = isset( $first['row'] )
					? (int) $first['row']
					: 100;

				$second_row = isset( $second['row'] )
					? (int) $second['row']
					: 100;

				if ( $first_row !== $second_row ) {
					return $first_row <=> $second_row;
				}

				$first_priority = isset( $first['priority'] )
					? (int) $first['priority']
					: 100;

				$second_priority = isset( $second['priority'] )
					? (int) $second['priority']
					: 100;

				if ( $first_priority !== $second_priority ) {
					return $first_priority <=> $second_priority;
				}

				return strcmp(
					(string) $first['id'],
					(string) $second['id']
				);
			}
		);

		/**
		 * Filter registered dashboard panels.
		 *
		 * @param array $panels Available dashboard panels.
		 */
		return apply_filters(
			'goug_dashboard_panels',
			$panels
		);
	}

	/**
	 * Return the current user's default dashboard layout profile.
	 *
	 * Profiles are resolved through native WordPress capabilities rather
	 * than role names. Custom roles therefore inherit the most appropriate
	 * layout automatically.
	 *
	 * @return string
	 */
	private function get_layout_profile() {

		if ( current_user_can( 'manage_options' ) ) {
			return 'administration';
		}

		if ( current_user_can( 'edit_pages' ) ) {
			return 'editorial';
		}

		if ( current_user_can( 'edit_posts' ) ) {
			return 'content';
		}

		return 'basic';
	}

	/**
	 * Return panel layout overrides for a dashboard profile.
	 *
	 * The administration profile uses the panel metadata registered by
	 * each panel and therefore requires no overrides.
	 *
	 * @param string $profile Dashboard layout profile.
	 *
	 * @return array
	 */
	private function get_layout_overrides( $profile ) {

		$layouts = array(
			'basic' => array(),

			'content' => array(
				'at-a-glance' => array(
					'row'      => 1,
					'width'    => 'half',
					'priority' => 10,
				),
				'quick-actions' => array(
					'row'      => 1,
					'width'    => 'half',
					'priority' => 20,
				),
				'recent-activity' => array(
					'row'      => 2,
					'width'    => 'full',
					'priority' => 10,
				),
				'quick-draft' => array(
					'row'      => 3,
					'width'    => 'two-thirds',
					'priority' => 10,
				),
			),

			'editorial' => array(
				'at-a-glance' => array(
					'row'      => 1,
					'width'    => 'half',
					'priority' => 10,
				),
				'quick-actions' => array(
					'row'      => 1,
					'width'    => 'half',
					'priority' => 20,
				),
				'recent-activity' => array(
					'row'      => 2,
					'width'    => 'full',
					'priority' => 10,
				),
				'quick-draft' => array(
					'row'      => 3,
					'width'    => 'two-thirds',
					'priority' => 10,
				),
			),

			'administration' => array(),
		);

		/**
		 * Filter the available dashboard layout profiles.
		 *
		 * @param array  $layouts All dashboard layout profiles.
		 * @param string $profile Current profile identifier.
		 */
		$layouts = apply_filters(
			'goug_dashboard_layout_profiles',
			$layouts,
			$profile
		);

		return isset( $layouts[ $profile ] )
			&& is_array( $layouts[ $profile ] )
				? $layouts[ $profile ]
				: array();
	}

	/**
	 * Apply the current user's default layout profile.
	 *
	 * Panel capability checks occur before this method runs. Layout
	 * overrides therefore affect only panels already available to the
	 * current user.
	 *
	 * @param array $panels Available dashboard panels.
	 *
	 * @return array
	 */
	private function apply_layout_profile( $panels ) {

		if ( ! is_array( $panels ) || empty( $panels ) ) {
			return array();
		}

		$profile   = $this->get_layout_profile();
		$overrides = $this->get_layout_overrides(
			$profile
		);

		if ( empty( $overrides ) ) {
			return $panels;
		}

		foreach ( $overrides as $panel_id => $layout ) {

			if (
				! isset( $panels[ $panel_id ] )
				|| ! is_array( $layout )
			) {
				continue;
			}

			$panels[ $panel_id ] = $this->apply_panel_layout(
				$panels[ $panel_id ],
				$layout
			);
		}

		return $panels;
	}

	/**
	 * Apply layout metadata to one panel.
	 *
	 * Updates the panel metadata, semantic width class, and row attribute
	 * together so the rendered panel remains internally consistent.
	 *
	 * @param array $panel  Normalized panel definition.
	 * @param array $layout Layout override.
	 *
	 * @return array
	 */
	private function apply_panel_layout(
		$panel,
		$layout ) {

		if ( isset( $layout['row'] ) ) {
			$panel['row'] = max(
				1,
				(int) $layout['row']
			);
		}

		if ( isset( $layout['priority'] ) ) {
			$panel['priority'] = max(
				0,
				(int) $layout['priority']
			);
		}

		if ( isset( $layout['width'] ) ) {
			$width = $this->normalize_width(
				$layout['width']
			);

			$panel['width']      = $width;
			$panel['class_name'] = $this->replace_width_class(
				$panel['class_name'],
				$width
			);
		}

		$panel['attributes']['data-panel-row'] = (string) $panel['row'];
		$panel['attributes']['data-layout-profile'] =
			$this->get_layout_profile();

		return $panel;
	}

	/**
	 * Normalize and validate a panel definition.
	 *
	 * @param array $panel Raw panel definition.
	 *
	 * @return array
	 */
	private function normalize_panel( array $panel ) {

		$defaults = array(
			'id'         => '',
			'title'      => '',
			'icon'       => '',
			'icon_svg'   => '',
			'width'      => 'full',
			'row'        => 100,
			'priority'   => 100,
			'class_name' => '',
			'body_view'  => '',
			'body_data'  => array(),
			'capability' => 'read',
			'profiles'   => array(),
			'visible'    => true,
			'attributes' => array(),
		);

		$panel = wp_parse_args(
			$panel,
			$defaults
		);

		$panel['id']         = sanitize_key( $panel['id'] );
		$panel['title']      = (string) $panel['title'];
		$panel['icon']       = sanitize_html_class( $panel['icon'] );
		$panel['icon_svg'] 	 = sanitize_file_name(
			(string) $panel['icon_svg']
		);
		$panel['width'] = $this->normalize_width(
			$panel['width']
		);

		$panel['row'] = max(
			1,
			(int) $panel['row']
		);
		$panel['priority'] = max(
			0,
			(int) $panel['priority']
		);
		$panel['class_name'] = (string) $panel['class_name'];
		$panel['body_view']  = trim( (string) $panel['body_view'] );
		$panel['body_data']  = is_array( $panel['body_data'] )
			? $panel['body_data']
			: array();
        $panel['attributes'] = is_array( $panel['attributes'] )
            ? $panel['attributes']
            : array();
		$panel['capability'] = (string) $panel['capability'];
		$panel['profiles'] = is_array( $panel['profiles'] )
			? array_values(
				array_filter(
					array_map(
						'sanitize_key',
						$panel['profiles']
					)
				)
			)
			: array();
		$panel['visible']    = (bool) $panel['visible'];

		if (
			'' === $panel['id'] ||
			'' === $panel['title'] ||
			'' === $panel['body_view']
		) {
			return array();
		}

		$panel['attributes']['data-panel-row'] = (string) $panel['row'];

		$panel['class_name'] = $this->replace_width_class(
			$panel['class_name'],
			$panel['width']
		);

		return $panel;
	}

	/**
	 * Replace a panel's semantic width class.
	 *
	 * @param string $class_name Existing panel classes.
	 * @param string $width      Normalized semantic width.
	 *
	 * @return string
	 */
	private function replace_width_class(
		$class_name,
		$width ) {

		$class_name = preg_replace(
			'/\bgoug-panel--width-(?:full|half|third|quarter)\b/',
			'',
			(string) $class_name
		);

		return trim(
			sprintf(
				'%s goug-panel--width-%s',
				$class_name,
				$width
			)
		);
	}

	/**
	 * Determine whether a panel should be returned.
	 *
	 * Panels must be visible, available to the current user's capability,
	 * and compatible with the active dashboard layout profile.
	 *
	 * An empty profiles array makes the panel available to every profile.
	 *
	 * @param array $panel Panel definition.
	 *
	 * @return bool
	 */
	private function is_panel_available( $panel ) {

		if (
			! is_array( $panel ) ||
			empty( $panel['visible'] )
		) {
			return false;
		}

		$capability = ! empty( $panel['capability'] )
			? $panel['capability']
			: 'read';

		if ( ! current_user_can( $capability ) ) {
			return false;
		}

		$profiles = isset( $panel['profiles'] )
			&& is_array( $panel['profiles'] )
				? $panel['profiles']
				: array();

		if (
			! empty( $profiles ) &&
			! in_array(
				$this->get_layout_profile(),
				$profiles,
				true
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Normalize a semantic dashboard panel width.
	 *
	 * @param string $width Requested width.
	 *
	 * @return string
	 */
	private function normalize_width( $width ) {

		$allowed_widths = array(
			'full',
			'half',
			'third',
			'two-thirds',
			'quarter',
		);

		$width = strtolower(
			trim(
				(string) $width
			)
		);

		return in_array(
			$width,
			$allowed_widths,
			true
		)
			? $width
			: 'full';
	}
}