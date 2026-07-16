<?php
/**
 * Dashboard user preferences service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

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
	 * Return the framework's default dashboard preferences.
	 *
	 * @return array
	 */
	public function get_defaults() {

		$defaults = array(
			'hidden_panels'   => array(),
			'panel_order'     => array(),
			'collapsed_panels' => array(),
			'density'         => 'comfortable',
			'show_greeting'   => true,
			'enable_motion'   => true,
		);

		/**
		 * Filter default dashboard preferences.
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
		$user_id = 0
	) {

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
		$user_id = 0
	) {

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
		$user_id = 0
	) {

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
	 * Sanitize a complete dashboard preference collection.
	 *
	 * @param array $preferences Raw dashboard preferences.
	 *
	 * @return array
	 */
	private function sanitize_preferences(
		array $preferences
	) {

		$defaults = $this->get_defaults();

		$preferences = array_intersect_key(
			$preferences,
			$defaults
		);

		return array(
			'hidden_panels' => $this->sanitize_panel_ids(
				$preferences['hidden_panels'] ?? array()
			),

			'panel_order' => $this->sanitize_panel_order(
				$preferences['panel_order'] ?? array()
			),

			'collapsed_panels' => $this->sanitize_panel_ids(
				$preferences['collapsed_panels'] ?? array()
			),

			'density' => $this->sanitize_density(
				$preferences['density'] ?? 'comfortable'
			),

			'show_greeting' => ! empty(
				$preferences['show_greeting']
			),

			'enable_motion' => ! empty(
				$preferences['enable_motion']
			),
		);
	}

	/**
	 * Sanitize a list of panel identifiers.
	 *
	 * @param mixed $panel_ids Raw panel identifiers.
	 *
	 * @return array
	 */
	private function sanitize_panel_ids( $panel_ids ) {

		if ( ! is_array( $panel_ids ) ) {
			return array();
		}

		$panel_ids = array_map(
			'sanitize_key',
			$panel_ids
		);

		$panel_ids = array_filter(
			$panel_ids
		);

		return array_values(
			array_unique(
				$panel_ids
			)
		);
	}

	/**
	 * Sanitize saved panel ordering.
	 *
	 * The structure maps panel IDs to row and priority values.
	 *
	 * @param mixed $panel_order Raw panel ordering.
	 *
	 * @return array
	 */
	private function sanitize_panel_order( $panel_order ) {

		if ( ! is_array( $panel_order ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( $panel_order as $panel_id => $layout ) {

			$panel_id = sanitize_key(
				$panel_id
			);

			if (
				'' === $panel_id ||
				! is_array( $layout )
			) {
				continue;
			}

			$sanitized[ $panel_id ] = array(
				'row' => isset( $layout['row'] )
					? max( 1, (int) $layout['row'] )
					: 1,

				'priority' => isset( $layout['priority'] )
					? max( 0, (int) $layout['priority'] )
					: 100,

				'width' => $this->sanitize_width(
					$layout['width'] ?? 'full'
				),
			);
		}

		return $sanitized;
	}

	/**
	 * Sanitize a dashboard density value.
	 *
	 * @param mixed $density Raw density value.
	 *
	 * @return string
	 */
	private function sanitize_density( $density ) {

		$density = sanitize_key(
			(string) $density
		);

		$allowed = array(
			'compact',
			'comfortable',
			'spacious',
		);

		return in_array(
			$density,
			$allowed,
			true
		)
			? $density
			: 'comfortable';
	}

	/**
	 * Sanitize a semantic panel width.
	 *
	 * @param mixed $width Raw width value.
	 *
	 * @return string
	 */
	private function sanitize_width( $width ) {

		$width = sanitize_key(
			(string) $width
		);

		$allowed = array(
			'full',
			'half',
			'third',
			'quarter',
		);

		return in_array(
			$width,
			$allowed,
			true
		)
			? $width
			: 'full';
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