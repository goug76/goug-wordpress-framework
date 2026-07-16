<?php
/**
 * Dashboard user preferences service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Settings\Settings_Manager;

/**
 * Stores and retrieves per-user dashboard preferences.
 *
 * Responsibilities:
 *
 * - Define dashboard preference defaults.
 * - Retrieve saved user preferences.
 * - Sanitize preference values.
 * - Merge saved values with framework defaults.
 * - Update and reset user preferences.
 *
 * Preferences are stored using native WordPress user metadata.
 */
class User_Preferences_Service {

	/**
	 * User meta key used to store dashboard preferences.
	 *
	 * @var string
	 */
	private const META_KEY = 'goug_dashboard_preferences';

	/**
	 * Cached preferences for the current request.
	 *
	 * Preferences are cached by user ID.
	 *
	 * @var array
	 */
	private $preferences = array();

	/**
	 * Framework settings manager.
	 *
	 * @var Settings_Manager
	 */
	private $settings_manager;

	/**
	 * Initialize the user preference service.
	 *
	 * @param Settings_Manager $settings_manager Framework settings manager.
	 */
	public function __construct(
		Settings_Manager $settings_manager
	) {

		$this->settings_manager = $settings_manager;
	}

	/**
	 * Return the default dashboard preferences.
	 *
	 * Dashboard defaults are defined by the framework Settings Registry.
	 * Namespaced setting identifiers are converted to the short keys used
	 * by the existing user-meta storage structure.
	 *
	 * @return array
	 */
	public function get_defaults() {

		$registered_defaults = $this->settings_manager->get_defaults(
			'user'
		);

		$defaults = $this->extract_dashboard_defaults(
			$registered_defaults
		);

		/**
		 * Filter default dashboard preferences.
		 *
		 * This existing filter remains available for backward compatibility.
		 *
		 * @param array $defaults Default dashboard preferences.
		 */
		$defaults = apply_filters(
			'goug_dashboard_preference_defaults',
			$defaults
		);

		return is_array( $defaults )
			? $defaults
			: array();
	}

	/**
	 * Return preferences for a user.
	 *
	 * Saved preferences are merged with the current framework defaults
	 * so newly introduced settings remain available automatically.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return array
	 */
	public function get_preferences( $user_id = 0 ) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		if ( 0 === $user_id ) {
			return $this->get_defaults();
		}

		if ( isset( $this->preferences[ $user_id ] ) ) {
			return $this->preferences[ $user_id ];
		}

		$saved_preferences = get_user_meta(
			$user_id,
			self::META_KEY,
			true
		);

		$saved_preferences = is_array( $saved_preferences )
			? $saved_preferences
			: array();

		$preferences = wp_parse_args(
			$saved_preferences,
			$this->get_defaults()
		);

		$this->preferences[ $user_id ] = $this->sanitize_preferences(
			$preferences
		);

		return $this->preferences[ $user_id ];
	}

	/**
	 * Return one preference value.
	 *
	 * @param string $key     Preference key.
	 * @param int    $user_id Optional user ID. Defaults to current user.
	 *
	 * @return mixed|null
	 */
	public function get_preference(
		$key,
		$user_id = 0 ) {

		$key         = sanitize_key( $key );
		$preferences = $this->get_preferences(
			$user_id
		);

		return array_key_exists( $key, $preferences )
			? $preferences[ $key ]
			: null;
	}

	/**
	 * Update dashboard preferences for a user.
	 *
	 * Only recognized preference keys are stored.
	 *
	 * @param array $preferences Preferences to update.
	 * @param int   $user_id     Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function update_preferences(
		array $preferences,
		$user_id = 0 ) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		if ( 0 === $user_id ) {
			return false;
		}

		if ( ! $this->can_manage_preferences( $user_id ) ) {
			return false;
		}

		$current = $this->get_preferences(
			$user_id
		);

		$updated = array_merge(
			$current,
			$preferences
		);

		$updated = $this->sanitize_preferences(
			$updated
		);

		$result = update_user_meta(
			$user_id,
			self::META_KEY,
			$updated
		);

		/*
		 * update_user_meta() may return false when the stored value is
		 * already identical. The operation is still considered valid.
		 */
		if ( false === $result ) {

			$stored = get_user_meta(
				$user_id,
				self::META_KEY,
				true
			);

			if ( $stored !== $updated ) {
				return false;
			}
		}

		$this->preferences[ $user_id ] = $updated;

		return true;
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

		$key = sanitize_key( $key );

		if ( '' === $key ) {
			return false;
		}

		return $this->update_preferences(
			array(
				$key => $value,
			),
			$user_id
		);
	}

	/**
	 * Reset dashboard preferences for a user.
	 *
	 * @param int $user_id Optional user ID. Defaults to current user.
	 *
	 * @return bool
	 */
	public function reset_preferences( $user_id = 0 ) {

		$user_id = $this->normalize_user_id(
			$user_id
		);

		if ( 0 === $user_id ) {
			return false;
		}

		if ( ! $this->can_manage_preferences( $user_id ) ) {
			return false;
		}

		delete_user_meta(
			$user_id,
			self::META_KEY
		);

		unset(
			$this->preferences[ $user_id ]
		);

		return true;
	}

    /**
     * Hide a dashboard panel for a user.
     *
     * @param string $panel_id Panel identifier.
     * @param int    $user_id  Optional user ID. Defaults to current user.
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
     * @param int    $user_id  Optional user ID. Defaults to current user.
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

        if ( ! is_array( $hidden_panels ) ) {
            return true;
        }

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
     * Determine whether a panel is hidden for a user.
     *
     * @param string $panel_id Panel identifier.
     * @param int    $user_id  Optional user ID. Defaults to current user.
     *
     * @return bool
     */
    public function is_panel_hidden(
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

        return is_array( $hidden_panels )
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

        if ( ! is_array( $collapsed_panels ) ) {
            return true;
        }

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
	 * Convert registered dashboard defaults to stored preference keys.
	 *
	 * The Settings Registry uses namespaced identifiers such as
	 * `dashboard.density`, while the existing user-meta record stores
	 * the shorter `density` key.
	 *
	 * Settings outside the dashboard namespace are ignored.
	 *
	 * @param array $registered_defaults Registered setting defaults.
	 *
	 * @return array
	 */
	private function extract_dashboard_defaults(
		array $registered_defaults ) {

		$defaults = array();
		$prefix   = 'dashboard.';

		foreach ( $registered_defaults as $setting_id => $default_value ) {

			$setting_id = (string) $setting_id;

			if ( 0 !== strpos( $setting_id, $prefix ) ) {
				continue;
			}

			$preference_key = substr(
				$setting_id,
				strlen( $prefix )
			);

			$preference_key = sanitize_key(
				$preference_key
			);

			if ( '' === $preference_key ) {
				continue;
			}

			$defaults[ $preference_key ] = $default_value;
		}

		return $defaults;
	}

	/**
	 * Return the registered setting ID for a dashboard preference.
	 *
	 * @param string $preference_key Stored preference key.
	 *
	 * @return string
	 */
	private function get_setting_id(
		$preference_key ) {

		$preference_key = sanitize_key(
			$preference_key
		);

		return '' !== $preference_key
			? 'dashboard.' . $preference_key
			: '';
	}

	/**
	 * Sanitize a complete dashboard preference collection.
	 *
	 * Registered dashboard setting definitions are the source of truth for
	 * defaults, field types, valid choices, and custom sanitization.
	 *
	 * @param array $preferences Raw dashboard preferences.
	 *
	 * @return array
	 */
	private function sanitize_preferences(
		array $preferences ) {

		$defaults = $this->get_defaults();

		$preferences = array_intersect_key(
			$preferences,
			$defaults
		);

		$registry  = $this->settings_manager->get_registry();
		$sanitized = array();

		foreach ( $defaults as $preference_key => $default_value ) {

			$value = array_key_exists(
				$preference_key,
				$preferences
			)
				? $preferences[ $preference_key ]
				: $default_value;

			$setting_id = $this->get_setting_id(
				$preference_key
			);

			$sanitized_value = $registry->sanitize_value(
				$setting_id,
				$value
			);

			$sanitized[ $preference_key ] =
				null !== $sanitized_value
					? $sanitized_value
					: $default_value;
		}

		return $sanitized;
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

	/**
	 * Determine whether the current user can update preferences.
	 *
	 * Users may update their own preferences. Administrators who can
	 * edit users may also update preferences for another user.
	 *
	 * @param int $user_id Target user ID.
	 *
	 * @return bool
	 */
	private function can_manage_preferences( $user_id ) {

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