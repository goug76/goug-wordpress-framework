<?php
/**
 * Framework user settings service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Settings\Services;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Settings\Settings_Manager;

/**
 * Stores and retrieves registered per-user framework settings.
 *
 * Responsibilities:
 *
 * - Retrieve registered user-scoped settings.
 * - Merge stored values with registered defaults.
 * - Sanitize values through the Settings Registry.
 * - Update one or several settings.
 * - Reset individual settings, sections, or all user settings.
 * - Persist values using native WordPress user metadata.
 *
 * This service only handles settings registered with a `user` scope.
 */
class User_Settings_Service {

	/**
	 * User meta key used to store framework settings.
	 *
	 * @var string
	 */
	private const META_KEY = 'goug_user_settings';

	/**
	 * Framework settings manager.
	 *
	 * @var Settings_Manager
	 */
	private $settings_manager;

	/**
	 * Cached settings by user ID.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Initialize the user settings service.
	 *
	 * @param Settings_Manager $settings_manager Framework settings manager.
	 */
	public function __construct(
		Settings_Manager $settings_manager
	) {

		$this->settings_manager = $settings_manager;
	}

	/**
	 * Return all framework user settings for a user.
	 *
	 * Saved values are merged with registered defaults so newly
	 * introduced settings are immediately available.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return array
	 */
	public function get_all( $user_id = 0 ) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		$defaults = $this->get_defaults();

		if ( 0 === $user_id ) {
			return $defaults;
		}

		if ( isset( $this->settings[ $user_id ] ) ) {
			return $this->settings[ $user_id ];
		}

		$saved_settings = get_user_meta(
			$user_id,
			self::META_KEY,
			true
		);

		$saved_settings = is_array( $saved_settings )
			? $saved_settings
			: array();

		$settings = array_merge(
			$defaults,
			array_intersect_key(
				$saved_settings,
				$defaults
			)
		);

		$this->settings[ $user_id ] = $this->sanitize_collection(
			$settings
		);

		return $this->settings[ $user_id ];
	}

	/**
	 * Return one registered user setting.
	 *
	 * @param string $setting_id Namespaced setting identifier.
	 * @param int    $user_id    Optional user ID. Defaults to current user.
	 *
	 * @return mixed|null
	 */
	public function get(
		$setting_id,
		$user_id = 0
	) {

		$setting_id = $this->normalize_setting_id(
			$setting_id
		);

		if ( '' === $setting_id ) {
			return null;
		}

		$settings = $this->get_all(
			$user_id
		);

		return array_key_exists(
			$setting_id,
			$settings
		)
			? $settings[ $setting_id ]
			: null;
	}

	/**
	 * Update one registered user setting.
	 *
	 * @param string $setting_id Namespaced setting identifier.
	 * @param mixed  $value      Raw setting value.
	 * @param int    $user_id    Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function set(
		$setting_id,
		$value,
		$user_id = 0
	) {

		return $this->set_many(
			array(
				$setting_id => $value,
			),
			$user_id
		);
	}

	/**
	 * Update several registered user settings.
	 *
	 * Unknown settings and settings with a non-user scope are ignored.
	 *
	 * @param array $settings Settings to update.
	 * @param int   $user_id  Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function set_many(
		array $settings,
		$user_id = 0
	) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		if (
			0 === $user_id ||
			! $this->can_manage_settings( $user_id )
		) {
			return false;
		}

		$current = $this->get_all(
			$user_id
		);

		$registry = $this->settings_manager->get_registry();

		foreach ( $settings as $setting_id => $value ) {

			$setting_id = $this->normalize_setting_id(
				$setting_id
			);

			if ( '' === $setting_id ) {
				continue;
			}

			$definition = $registry->get_setting(
				$setting_id
			);

			if (
				null === $definition ||
				'user' !== $definition['scope']
			) {
				continue;
			}

			$sanitized_value = $registry->sanitize_value(
				$setting_id,
				$value
			);

			if ( null === $sanitized_value ) {
				continue;
			}

			$current[ $setting_id ] = $sanitized_value;
		}

		return $this->persist(
			$user_id,
			$current
		);
	}

	/**
	 * Reset one setting to its registered default.
	 *
	 * The saved override is removed from user metadata. The registered
	 * default will be returned on the next read.
	 *
	 * @param string $setting_id Namespaced setting identifier.
	 * @param int    $user_id    Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function reset(
		$setting_id,
		$user_id = 0
	) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		$setting_id = $this->normalize_setting_id(
			$setting_id
		);

		if (
			0 === $user_id ||
			'' === $setting_id ||
			! $this->can_manage_settings( $user_id )
		) {
			return false;
		}

		$stored = $this->get_stored_settings(
			$user_id
		);

		unset(
			$stored[ $setting_id ]
		);

		return $this->persist(
			$user_id,
			$stored
		);
	}

	/**
	 * Reset every setting within a namespace section.
	 *
	 * For example, passing `dashboard` removes all saved settings whose
	 * identifiers begin with `dashboard.`.
	 *
	 * @param string $section User setting section.
	 * @param int    $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function reset_section(
		$section,
		$user_id = 0
	) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		$section = sanitize_key(
			$section
		);

		if (
			0 === $user_id ||
			'' === $section ||
			! $this->can_manage_settings( $user_id )
		) {
			return false;
		}

		$stored = $this->get_stored_settings(
			$user_id
		);

		$prefix = $section . '.';

		foreach ( array_keys( $stored ) as $setting_id ) {

			if ( 0 === strpos( $setting_id, $prefix ) ) {
				unset(
					$stored[ $setting_id ]
				);
			}
		}

		return $this->persist(
			$user_id,
			$stored
		);
	}

	/**
	 * Reset all framework user settings.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function reset_all( $user_id = 0 ) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		if (
			0 === $user_id ||
			! $this->can_manage_settings( $user_id )
		) {
			return false;
		}

		delete_user_meta(
			$user_id,
			self::META_KEY
		);

		unset(
			$this->settings[ $user_id ]
		);

		return true;
	}

	/**
	 * Return registered defaults for user-scoped settings.
	 *
	 * @return array
	 */
	private function get_defaults() {

		$defaults = $this->settings_manager->get_defaults(
			'user'
		);

		return is_array( $defaults )
			? $defaults
			: array();
	}

	/**
	 * Sanitize a complete user setting collection.
	 *
	 * @param array $settings Raw setting collection.
	 *
	 * @return array
	 */
	private function sanitize_collection(
		array $settings
	) {

		$defaults  = $this->get_defaults();
		$registry  = $this->settings_manager->get_registry();
		$sanitized = array();

		foreach ( $defaults as $setting_id => $default_value ) {

			$value = array_key_exists(
				$setting_id,
				$settings
			)
				? $settings[ $setting_id ]
				: $default_value;

			$sanitized_value = $registry->sanitize_value(
				$setting_id,
				$value
			);

			$sanitized[ $setting_id ] =
				null !== $sanitized_value
					? $sanitized_value
					: $default_value;
		}

		return $sanitized;
	}

	/**
	 * Return only values explicitly stored for a user.
	 *
	 * Registered defaults are not merged into this collection.
	 *
	 * @param int $user_id User ID.
	 *
	 * @return array
	 */
	private function get_stored_settings( $user_id ) {

		$stored = get_user_meta(
			$user_id,
			self::META_KEY,
			true
		);

		return is_array( $stored )
			? $stored
			: array();
	}

	/**
	 * Persist a user setting collection.
	 *
	 * @param int   $user_id User ID.
	 * @param array $settings User setting values.
	 *
	 * @return bool
	 */
	private function persist(
		$user_id,
		array $settings
	) {

		$defaults = $this->get_defaults();

		/*
		 * Keep only registered user-scoped setting identifiers.
		 */
		$settings = array_intersect_key(
			$settings,
			$defaults
		);

		$settings = $this->sanitize_collection(
			array_merge(
				$defaults,
				$settings
			)
		);

		$result = update_user_meta(
			$user_id,
			self::META_KEY,
			$settings
		);

		/*
		 * WordPress returns false when the stored value is unchanged.
		 * Treat an identical stored value as a successful operation.
		 */
		if ( false === $result ) {

			$stored = get_user_meta(
				$user_id,
				self::META_KEY,
				true
			);

			if ( $stored !== $settings ) {
				return false;
			}
		}

		$this->settings[ $user_id ] = $settings;

		return true;
	}

	/**
	 * Normalize a namespaced setting identifier.
	 *
	 * @param mixed $setting_id Raw setting identifier.
	 *
	 * @return string
	 */
	private function normalize_setting_id( $setting_id ) {

		$parts = explode(
			'.',
			strtolower(
				trim(
					(string) $setting_id
				)
			)
		);

		$parts = array_filter(
			array_map(
				'sanitize_key',
				$parts
			)
		);

		return implode(
			'.',
			$parts
		);
	}

	/**
	 * Return a valid user ID.
	 *
	 * @param int $user_id Optional user ID.
	 *
	 * @return int
	 */
	private function normalize_user_id( $user_id ) {

		$user_id = absint(
			$user_id
		);

		return 0 !== $user_id
			? $user_id
			: get_current_user_id();
	}

	/**
	 * Determine whether the current user may manage settings.
	 *
	 * Users may update their own settings. Users with permission to edit
	 * another account may manage that account's framework settings.
	 *
	 * @param int $user_id Target user ID.
	 *
	 * @return bool
	 */
	private function can_manage_settings( $user_id ) {

		$current_user_id = get_current_user_id();

		if ( $current_user_id === $user_id ) {
			return current_user_can( 'read' );
		}

		return current_user_can(
			'edit_user',
			$user_id
		);
	}
}