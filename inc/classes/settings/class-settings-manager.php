<?php
/**
 * Framework settings manager.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Settings;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Settings\Definitions\Dashboard_Settings;

/**
 * Coordinates framework setting registration.
 *
 * Responsibilities:
 *
 * - Create the framework Settings Registry.
 * - Register built-in setting definitions.
 * - Expose registered definitions to framework consumers.
 *
 * The manager does not persist setting values.
 */
class Settings_Manager {

	/**
	 * Framework settings registry.
	 *
	 * @var Settings_Registry
	 */
	private $registry;

	/**
	 * Whether built-in settings have been registered.
	 *
	 * @var bool
	 */
	private $settings_registered = false;

	/**
	 * Initialize the framework settings manager.
	 */
	public function __construct() {

		$this->registry = new Settings_Registry();
	}

	/**
	 * Return the framework settings registry.
	 *
	 * @return Settings_Registry
	 */
	public function get_registry() {

		$this->register_default_settings();

		return $this->registry;
	}

	/**
	 * Return all registered setting definitions.
	 *
	 * @return array
	 */
	public function get_settings() {

		return $this->get_registry()->get_settings();
	}

	/**
	 * Return all registered setting defaults.
	 *
	 * @param string $scope Optional setting storage scope.
	 *
	 * @return array
	 */
	public function get_defaults( $scope = '' ) {

		return $this->get_registry()->get_defaults(
			$scope
		);
	}

	/**
	 * Register built-in framework setting definitions.
	 *
	 * @return void
	 */
	private function register_default_settings() {

		if ( $this->settings_registered ) {
			return;
		}

		$this->settings_registered = true;

		$dashboard_settings = new Dashboard_Settings();

		$dashboard_settings->register(
			$this->registry
		);

		/**
		 * Fires after built-in framework settings are registered.
		 *
		 * External modules may register additional settings through the
		 * provided registry.
		 *
		 * @param Settings_Registry $registry Settings registry.
		 */
		do_action(
			'goug_framework_register_settings',
			$this->registry
		);
	}
}