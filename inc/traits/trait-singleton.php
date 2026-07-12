<?php
/**
 * Singleton trait.
 *
 * Provides one instance per implementing class during the current request.
 * Intended primarily for theme services and bootstrappers that register
 * WordPress hooks and must only be initialized once.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Traits;

defined( 'ABSPATH' ) || exit;

trait Singleton {

	/**
	 * Prevent direct construction.
	 *
	 * Implementing classes may override this method while keeping it protected.
	 */
	protected function __construct() {
	}

	/**
	 * Prevent cloning.
	 *
	 * @return void
	 */
	final protected function __clone() {
	}

	/**
	 * Prevent unserialization.
	 *
	 * @return void
	 */
	final public function __wakeup() {
		throw new \LogicException(
			sprintf(
				'Unserializing singleton class %s is not allowed.',
				static::class
			)
		);
	}

	/**
	 * Return the singleton instance for the called class.
	 *
	 * @return static
	 */
	final public static function get_instance() {
		static $instances = array();

		$called_class = static::class;

		if ( ! isset( $instances[ $called_class ] ) ) {
			$instances[ $called_class ] = new static();

			$hook_class = strtolower(
				str_replace( '\\', '_', $called_class )
			);

			/**
			 * Fires after a singleton class has been initialized.
			 *
			 * The dynamic portion of the hook is the normalized class name.
			 *
			 * Example:
			 * goug_theme_singleton_init_goug_inc_dashboard
			 *
			 * @param object $instance Initialized singleton instance.
			 */
			do_action(
				"goug_theme_singleton_init_{$hook_class}",
				$instances[ $called_class ]
			);
		}

		return $instances[ $called_class ];
	}
}