<?php
/**
 * Dashboard Preferences summary panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;
use GOUG\Inc\Dashboard\Services\User_Preferences_Service;

/**
 * Registers and prepares the Dashboard Preferences panel.
 */
class Panel_Dashboard_Preferences implements Dashboard_Panel {

	/**
	 * Dashboard preference service.
	 *
	 * @var User_Preferences_Service
	 */
	private $preferences_service;

	/**
	 * Initialize the panel.
	 *
	 * @param User_Preferences_Service $preferences_service
	 *        Dashboard preference service.
	 */
	public function __construct(
		User_Preferences_Service $preferences_service
	) {

		$this->preferences_service = $preferences_service;
	}

	/**
	 * Register the Dashboard Preferences panel.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$registry->register_panel(
			array(
				'id'         => 'dashboard-preferences',
				'title'      => __(
					'Dashboard Preferences',
					'goug-framework'
				),
				'icon'       => 'dashicons-admin-generic',
				'row'        => 5,
				'width'      => 'third',
				'priority'   => 90,
				'class_name' => 'goug-panel--dashboard-preferences',
				'body_view'  => 'dashboard/components/dashboard-preferences',
				'body_data' => $this->get_data(
					$registry
				),
				'capability' => 'read',
				'attributes' => array(
					'data-panel-id' => 'dashboard-preferences',
				),
			)
		);
	}

	/**
	 * Return prepared dashboard preference data.
	 *
	 * @param Dashboard_Registry $registry Dashboard panel registry.
	 *
	 * @return array
	 */
	private function get_data( Dashboard_Registry $registry ) {

		$preferences = $this->preferences_service->get_preferences();

		$density = isset( $preferences['density'] )
			? sanitize_key( $preferences['density'] )
			: 'comfortable';

		$allowed_densities = array(
			'compact',
			'comfortable',
			'spacious',
		);

		if ( ! in_array( $density, $allowed_densities, true ) ) {
			$density = 'comfortable';
		}

		$density_labels = array(
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
		);

		$hidden_panels = isset( $preferences['hidden_panels'] )
			&& is_array( $preferences['hidden_panels'] )
				? $preferences['hidden_panels']
				: array();

		$available_panels = array();

		foreach ( $registry->get_panels() as $panel_id => $panel ) {

			if ( ! is_array( $panel ) ) {
				continue;
			}

			$panel_id = sanitize_key(
				$panel_id
			);

			if ( '' === $panel_id ) {
				continue;
			}

			$available_panels[ $panel_id ] = array(
				'id'      => $panel_id,
				'title'   => isset( $panel['title'] )
					? (string) $panel['title']
					: $panel_id,
				'visible' => ! in_array(
					$panel_id,
					$hidden_panels,
					true
				),
			);
		}

		$data = array(
			'density' => array(
				'label' => __(
					'Density',
					'goug-framework'
				),
				'value' => $density_labels[ $density ],
				'icon'  => 'dashicons-editor-justify',
			),
			'greeting' => array(
				'label'   => __(
					'Greeting',
					'goug-framework'
				),
				'value'   => ! empty( $preferences['show_greeting'] )
					? __( 'Enabled', 'goug-framework' )
					: __( 'Disabled', 'goug-framework' ),
				'icon'    => 'dashicons-admin-users',
				'enabled' => ! empty(
					$preferences['show_greeting']
				),
			),
			'animations' => array(
				'label'   => __(
					'Animations',
					'goug-framework'
				),
				'value'   => ! empty( $preferences['enable_motion'] )
					? __( 'Enabled', 'goug-framework' )
					: __( 'Disabled', 'goug-framework' ),
				'icon'    => 'dashicons-image-rotate',
				'enabled' => ! empty(
					$preferences['enable_motion']
				),
			),
			'hidden_panels' => array(
				'label' => __(
					'Hidden Panels',
					'goug-framework'
				),
				'value' => count( $hidden_panels ),
				'icon'  => 'dashicons-hidden',
			),
			'modal' => array(
				'id'               => 'goug-dashboard-preferences-modal',
				'title'            => __(
					'Configure Dashboard',
					'goug-framework'
				),
				'density'          => $density,
				'density_options'  => $density_labels,
				'show_greeting'    => ! empty(
					$preferences['show_greeting']
				),
				'enable_motion'    => ! empty(
					$preferences['enable_motion']
				),
				'hidden_panels'    => count( $hidden_panels ),
				'available_panels' => $available_panels,
			),
		);

		/**
		 * Filter Dashboard Preferences panel data.
		 *
		 * @param array $data        Prepared preference data.
		 * @param array $preferences Current dashboard preferences.
		 */
		$data = apply_filters(
			'goug_dashboard_preferences_panel_data',
			$data,
			$preferences
		);

		return is_array( $data )
			? $data
			: array();
	}
}