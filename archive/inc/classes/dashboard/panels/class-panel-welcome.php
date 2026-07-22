<?php
/**
 * Dashboard Welcome panel.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;

/**
 * Registers the Basic dashboard welcome panel.
 */
class Panel_Welcome implements Dashboard_Panel {

	/**
	 * Register the Welcome panel.
	 *
	 * This panel provides a simple orientation message for users whose
	 * accounts do not include content or administrative capabilities.
	 *
	 * @param Dashboard_Registry $registry Dashboard registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry ) {

		$registry->register_panel(
			array(
				'id'         => 'welcome',
				'title'      => __( 'Welcome', 'goug-framework' ),
				'icon'       => 'dashicons-smiley',
				'width'      => 'two-thirds',
				'priority'   => 10,
				'class_name' => 'goug-panel--welcome',
				'body_view'  => 'dashboard/components/welcome',
				'body_data'  => array(
					'site_name'   => get_bloginfo( 'name' ),
					'description' => get_bloginfo( 'description' ),
					'profile_url' => admin_url( 'profile.php' ),
				),
				'capability' => 'read',
				'profiles'   => array( 'basic' ),
				'attributes' => array(
					'data-panel-id' => 'welcome',
				),
			)
		);
	}
}