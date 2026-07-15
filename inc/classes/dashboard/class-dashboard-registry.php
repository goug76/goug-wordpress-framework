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
	 * Panels are sorted from lowest to highest priority.
	 *
	 * @return array
	 */
	public function get_panels() {

		$panels = array_filter(
			$this->panels,
			array( $this, 'is_panel_available' )
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
		$allowed_widths = array(
			'full',
			'half',
			'third',
			'quarter',
		);

		$panel['width'] = strtolower(
			trim(
				(string) $panel['width']
			)
		);

		if (
			! in_array(
				$panel['width'],
				$allowed_widths,
				true
			)
		) {
			$panel['width'] = 'full';
		}
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
		$panel['visible']    = (bool) $panel['visible'];

		if (
			'' === $panel['id'] ||
			'' === $panel['title'] ||
			'' === $panel['body_view']
		) {
			return array();
		}

		$panel['attributes']['data-panel-row'] = (string) $panel['row'];

		$panel['class_name'] = trim(
			sprintf(
				'%s goug-panel--width-%s',
				$panel['class_name'],
				$panel['width']
			)
		);

		return $panel;
	}

	/**
	 * Determine whether a panel should be returned.
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

		return current_user_can( $capability );
	}
}