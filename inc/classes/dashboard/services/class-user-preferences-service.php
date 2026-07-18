<?php
/**
 * Dashboard user preferences service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Settings\Services\User_Settings_Service;

/**
 * Provides a dashboard-specific facade over framework user settings.
 *
 * Persistence, defaults, permissions, and sanitization are handled by
 * User_Settings_Service and the Settings Registry.
 */
class User_Preferences_Service {

	/**
	 * Generic framework user settings service.
	 *
	 * @var User_Settings_Service
	 */
	private $user_settings_service;

	/**
	 * Legacy dashboard preference meta key.
	 *
	 * @var string
	 */
	private const LEGACY_META_KEY = 'goug_dashboard_preferences';

	/**
	 * Framework user settings meta key.
	 *
	 * Used only to determine whether legacy preferences should migrate.
	 *
	 * @var string
	 */
	private const USER_SETTINGS_META_KEY = 'goug_user_settings';

	/**
	 * Initialize the dashboard preference facade.
	 *
	 * @param User_Settings_Service $user_settings_service
	 *        Generic framework user settings service.
	 */
	public function __construct(
		User_Settings_Service $user_settings_service ) {

		$this->user_settings_service = $user_settings_service;
	}

	/**
	 * Migrate legacy dashboard preferences to framework user settings.
	 *
	 * Migration runs only when legacy data exists and the new dashboard
	 * settings have not already been customized.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function migrate_legacy_preferences(
		$user_id = 0 ) {

		$user_id = absint( $user_id );

		if ( 0 === $user_id ) {
			$user_id = get_current_user_id();
		}

		if ( 0 === $user_id ) {
			return false;
		}

		$legacy = get_user_meta(
			$user_id,
			self::LEGACY_META_KEY,
			true
		);

		if ( ! is_array( $legacy ) || empty( $legacy ) ) {
			return true;
		}

		$new_settings = get_user_meta(
			$user_id,
			self::USER_SETTINGS_META_KEY,
			true
		);

		/*
		 * Do not overwrite settings already written to the new store.
		 */
		if ( is_array( $new_settings ) && ! empty( $new_settings ) ) {
			return true;
		}

		$result = $this->update_preferences(
			$legacy,
			$user_id
		);

		if ( ! $result ) {
			return false;
		}

		delete_user_meta(
			$user_id,
			self::LEGACY_META_KEY
		);

		return true;
	}


	/**
	 * Return dashboard preferences for a user.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return array
	 */
	public function get_preferences( $user_id = 0 ) {

		return $this->get_dashboard_settings(
			$user_id
		);
	}

	/**
	 * Return one dashboard preference.
	 *
	 * @param string $key     Preference key.
	 * @param int    $user_id Optional user ID. Defaults to current user.
	 *
	 * @return mixed|null
	 */
	public function get_preference(
		$key,
		$user_id = 0 ) {

		$setting_id = $this->get_setting_id(
			$key
		);

		if ( '' === $setting_id ) {
			return null;
		}

		return $this->user_settings_service->get(
			$setting_id,
			$user_id
		);
	}

	/**
	 * Update one dashboard preference.
	 *
	 * @param string $key     Preference key.
	 * @param mixed  $value   Preference value.
	 * @param int    $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function update_preference(
		$key,
		$value,
		$user_id = 0 ) {

		$setting_id = $this->get_setting_id(
			$key
		);

		if ( '' === $setting_id ) {
			return false;
		}

		return $this->user_settings_service->set(
			$setting_id,
			$value,
			$user_id
		);
	}

	/**
	 * Update several dashboard preferences.
	 *
	 * @param array $preferences Preferences keyed by short dashboard names.
	 * @param int   $user_id     Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function update_preferences(
		array $preferences,
		$user_id = 0 ) {

		$settings = array();

		foreach ( $preferences as $key => $value ) {

			$setting_id = $this->get_setting_id(
				$key
			);

			if ( '' === $setting_id ) {
				continue;
			}

			$settings[ $setting_id ] = $value;
		}

		if ( empty( $settings ) ) {
			return false;
		}

		return $this->user_settings_service->set_many(
			$settings,
			$user_id
		);
	}

	/**
	 * Reset all dashboard preferences for a user.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function reset_preferences( $user_id = 0 ) {

		return $this->user_settings_service->reset_section(
			'dashboard',
			$user_id
		);
	}

	/**
	 * Hide a dashboard panel for a user.
	 *
	 * @param string $panel_id Panel identifier.
	 * @param int    $user_id  Optional user ID.
	 *
	 * @return bool
	 */
	public function hide_panel(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
			$panel_id
		);

		if ( '' === $panel_id ) {
			return false;
		}

		$hidden_panels = $this->get_preference(
			'hidden_panels',
			$user_id
		);

		$hidden_panels = is_array( $hidden_panels )
			? $hidden_panels
			: array();

		if ( in_array( $panel_id, $hidden_panels, true ) ) {
			return true;
		}

		$hidden_panels[] = $panel_id;

		return $this->update_preference(
			'hidden_panels',
			$hidden_panels,
			$user_id
		);
	}

	/**
	 * Show a previously hidden dashboard panel.
	 *
	 * @param string $panel_id Panel identifier.
	 * @param int    $user_id  Optional user ID.
	 *
	 * @return bool
	 */
	public function show_panel(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
			$panel_id
		);

		if ( '' === $panel_id ) {
			return false;
		}

		$hidden_panels = $this->get_preference(
			'hidden_panels',
			$user_id
		);

		$hidden_panels = is_array( $hidden_panels )
			? $hidden_panels
			: array();

		$hidden_panels = array_values(
			array_diff(
				$hidden_panels,
				array( $panel_id )
			)
		);

		return $this->update_preference(
			'hidden_panels',
			$hidden_panels,
			$user_id
		);
	}

	/**
	 * Determine whether a panel is hidden.
	 *
	 * @param string $panel_id Panel identifier.
	 * @param int    $user_id  Optional user ID.
	 *
	 * @return bool
	 */
	public function is_panel_hidden(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
			$panel_id
		);

		$hidden_panels = $this->get_preference(
			'hidden_panels',
			$user_id
		);

		return '' !== $panel_id
			&& is_array( $hidden_panels )
			&& in_array(
				$panel_id,
				$hidden_panels,
				true
			);
	}

	/**
     * Collapse a dashboard panel for a user.
     *
     * @param string $panel_id Panel identifier.
     * @param int    $user_id  Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function collapse_panel(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
			$panel_id
		);

		if ( '' === $panel_id ) {
			return false;
		}

		$collapsed_panels = $this->get_preference(
			'collapsed_panels',
			$user_id
		);

		$collapsed_panels = is_array( $collapsed_panels )
			? $collapsed_panels
			: array();

		if ( in_array( $panel_id, $collapsed_panels, true ) ) {
			return true;
		}

		$collapsed_panels[] = $panel_id;

		return $this->update_preference(
			'collapsed_panels',
			$collapsed_panels,
			$user_id
		);
	}

	/**
     * Expand a previously collapsed dashboard panel.
     *
     * @param string $panel_id Panel identifier.
     * @param int    $user_id  Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function expand_panel(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
			$panel_id
		);

		if ( '' === $panel_id ) {
			return false;
		}

		$collapsed_panels = $this->get_preference(
			'collapsed_panels',
			$user_id
		);

		$collapsed_panels = is_array( $collapsed_panels )
			? $collapsed_panels
			: array();

		$collapsed_panels = array_values(
			array_diff(
				$collapsed_panels,
				array( $panel_id )
			)
		);

		return $this->update_preference(
			'collapsed_panels',
			$collapsed_panels,
			$user_id
		);
	}

	/**
     * Determine whether a panel is collapsed for a user.
     *
     * @param string $panel_id Panel identifier.
     * @param int    $user_id  Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function is_panel_collapsed(
		$panel_id,
		$user_id = 0 ) {

		$panel_id = sanitize_key(
            $panel_id
        );

        if ( '' === $panel_id ) {
            return false;
        }

		$collapsed_panels = $this->get_preference(
            'collapsed_panels',
            $user_id
        );

        return is_array( $collapsed_panels )
            && in_array(
                $panel_id,
                $collapsed_panels,
                true
            );
    }

	/**
     * Set the dashboard density for a user.
     *
     * @param string $density Density identifier.
     * @param int    $user_id Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function set_density(
		$density,
		$user_id = 0 ) {

		return $this->update_preference(
			'density',
			$density,
			$user_id
		);
	}

	/**
     * Set whether the dashboard greeting is visible.
     *
     * @param bool $visible Whether the greeting should be visible.
     * @param int  $user_id Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function set_greeting_visibility(
		$visible,
		$user_id = 0 ) {

		return $this->update_preference(
			'show_greeting',
			(bool) $visible,
			$user_id
		);
	}

	/**
     * Set whether dashboard motion effects are enabled.
     *
     * @param bool $enabled Whether motion effects should be enabled.
     * @param int  $user_id Optional user ID. Defaults to current user.
     *
     * @return bool
     */
	public function set_motion_enabled(
		$enabled,
		$user_id = 0 ) {

		return $this->update_preference(
			'enable_motion',
			(bool) $enabled,
			$user_id
		);
	}

	/**
	 * Return dashboard-scoped settings using short preference keys.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return array
	 */
	private function get_dashboard_settings( $user_id = 0 ) {

		$settings    = $this->user_settings_service->get_all(
			$user_id
		);
		$preferences = array();

		foreach ( $settings as $setting_id => $value ) {

			$preference_key = $this->get_preference_key(
				$setting_id
			);

			if ( '' === $preference_key ) {
				continue;
			}

			$preferences[ $preference_key ] = $value;
		}

		return $preferences;
	}

	/**
	 * Return the namespaced setting ID for a dashboard preference.
	 *
	 * @param string $preference_key Dashboard preference key.
	 *
	 * @return string
	 */
	private function get_setting_id( $preference_key ) {

		$preference_key = sanitize_key(
			$preference_key
		);

		return '' !== $preference_key
			? 'dashboard.' . $preference_key
			: '';
	}

	/**
	 * Remove the dashboard namespace from a setting identifier.
	 *
	 * @param string $setting_id Namespaced setting identifier.
	 *
	 * @return string
	 */
	private function get_preference_key( $setting_id ) {

		$setting_id = (string) $setting_id;
		$prefix     = 'dashboard.';

		if ( 0 !== strpos( $setting_id, $prefix ) ) {
			return '';
		}

		return sanitize_key(
			substr(
				$setting_id,
				strlen( $prefix )
			)
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

		$user_id = absint( $user_id );

		if ( 0 === $user_id ) {
			$user_id = get_current_user_id();
		}

		return $user_id;
	}

}