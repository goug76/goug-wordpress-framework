<?php
/**
 * Framework settings registry.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Settings;

defined( 'ABSPATH' ) || exit;

/**
 * Stores and normalizes framework setting definitions.
 *
 * Responsibilities:
 *
 * - Register framework settings.
 * - Normalize setting definitions.
 * - Validate required setting metadata.
 * - Provide setting lookup methods.
 * - Return registered default values.
 *
 * The registry stores definitions only.
 * It does not read or write setting values.
 */
class Settings_Registry {

	/**
	 * Registered setting definitions.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Register a framework setting.
	 *
	 * Required:
	 *
	 * - id
	 * - label
	 * - type
	 *
	 * Optional:
	 *
	 * - section
	 * - description
	 * - scope
	 * - default
	 * - choices
	 * - capability
	 * - visible
	 * - sanitize_callback
	 * - attributes
	 *
	 * @param array $setting Setting definition.
	 *
	 * @return bool
	 */
	public function register_setting( array $setting ) {

		$setting = $this->normalize_setting(
			$setting
		);

		if ( empty( $setting ) ) {
			return false;
		}

		$this->settings[ $setting['id'] ] = $setting;

		return true;
	}

	/**
	 * Remove a registered setting.
	 *
	 * @param string $setting_id Setting identifier.
	 *
	 * @return bool
	 */
	public function unregister_setting( $setting_id ) {

		$setting_id = $this->sanitize_setting_id(
			$setting_id
		);

		if (
			'' === $setting_id ||
			! isset( $this->settings[ $setting_id ] )
		) {
			return false;
		}

		unset( $this->settings[ $setting_id ] );

		return true;
	}

	/**
	 * Determine whether a setting is registered.
	 *
	 * @param string $setting_id Setting identifier.
	 *
	 * @return bool
	 */
	public function has_setting( $setting_id ) {

		$setting_id = $this->sanitize_setting_id(
			$setting_id
		);

		return '' !== $setting_id
			&& isset( $this->settings[ $setting_id ] );
	}

	/**
	 * Return one registered setting definition.
	 *
	 * @param string $setting_id Setting identifier.
	 *
	 * @return array|null
	 */
	public function get_setting( $setting_id ) {

		$setting_id = $this->sanitize_setting_id(
			$setting_id
		);

		return isset( $this->settings[ $setting_id ] )
			? $this->settings[ $setting_id ]
			: null;
	}

	/**
	 * Return all registered setting definitions.
	 *
	 * Settings are sorted by section, priority, and identifier.
	 *
	 * @return array
	 */
	public function get_settings() {

		$settings = $this->settings;

		uasort(
			$settings,
			static function ( $first, $second ) {

				$first_section = isset( $first['section'] )
					? (string) $first['section']
					: '';

				$second_section = isset( $second['section'] )
					? (string) $second['section']
					: '';

				if ( $first_section !== $second_section ) {
					return strcmp(
						$first_section,
						$second_section
					);
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
		 * Filter registered framework settings.
		 *
		 * @param array $settings Registered setting definitions.
		 */
		return apply_filters(
			'goug_framework_settings',
			$settings
		);
	}

	/**
	 * Return registered setting defaults.
	 *
	 * @param string $scope Optional storage scope filter.
	 *
	 * @return array
	 */
	public function get_defaults( $scope = '' ) {

		$scope   = sanitize_key( $scope );
		$defaults = array();

		foreach ( $this->get_settings() as $setting_id => $setting ) {

			if (
				'' !== $scope &&
				$scope !== $setting['scope']
			) {
				continue;
			}

			$defaults[ $setting_id ] = $setting['default'];
		}

		return $defaults;
	}

	/**
	 * Sanitize a value using its registered setting definition.
	 *
	 * A custom sanitize callback takes precedence. Otherwise, the value is
	 * sanitized according to the registered setting type.
	 *
	 * @param string $setting_id Setting identifier.
	 * @param mixed  $value      Raw setting value.
	 *
	 * @return mixed|null
	 */
	public function sanitize_value(
		$setting_id,
		$value ) {

		$setting = $this->get_setting(
			$setting_id
		);

		if ( null === $setting ) {
			return null;
		}

		if (
			isset( $setting['sanitize_callback'] ) &&
			is_callable( $setting['sanitize_callback'] )
		) {
			return call_user_func(
				$setting['sanitize_callback'],
				$value,
				$setting
			);
		}

		switch ( $setting['type'] ) {

			case 'checkbox':
				return (bool) $value;

			case 'number':
				return is_numeric( $value )
					? (float) $value
					: $setting['default'];

			case 'select':
				return $this->sanitize_choice(
					$value,
					$setting
				);

			case 'multiselect':
				return $this->sanitize_multiple_choices(
					$value,
					$setting
				);

			case 'textarea':
				return sanitize_textarea_field(
					(string) $value
				);

			case 'color':
				$color = sanitize_hex_color(
					(string) $value
				);

				return null !== $color
					? $color
					: $setting['default'];

			case 'text':
			default:
				return sanitize_text_field(
					(string) $value
				);
		}
	}

	/**
	 * Normalize and validate a setting definition.
	 *
	 * @param array $setting Raw setting definition.
	 *
	 * @return array
	 */
	private function normalize_setting( array $setting ) {

		$defaults = array(
			'id'                => '',
			'section'           => 'general',
			'label'             => '',
			'description'       => '',
			'type'              => 'text',
			'scope'             => 'user',
			'default'           => null,
			'choices'           => array(),
			'priority'          => 100,
			'capability'        => 'read',
			'visible'           => true,
			'sanitize_callback' => null,
			'attributes'        => array(),
		);

		$setting = wp_parse_args(
			$setting,
			$defaults
		);

		$setting['id'] = $this->sanitize_setting_id(
			$setting['id']
		);

		$setting['section'] = sanitize_key(
			$setting['section']
		);

		$setting['label'] = trim(
			(string) $setting['label']
		);

		$setting['description'] = trim(
			(string) $setting['description']
		);

		$setting['type'] = $this->normalize_type(
			$setting['type']
		);

		$setting['scope'] = $this->normalize_scope(
			$setting['scope']
		);

		$setting['choices'] = is_array( $setting['choices'] )
			? $setting['choices']
			: array();

		$setting['priority'] = max(
			0,
			(int) $setting['priority']
		);

		$setting['capability'] = '' !== $setting['capability']
			? sanitize_key( $setting['capability'] )
			: 'read';

		$setting['visible'] = (bool) $setting['visible'];

		$setting['attributes'] = is_array( $setting['attributes'] )
			? $setting['attributes']
			: array();

		if (
			null !== $setting['sanitize_callback'] &&
			! is_callable( $setting['sanitize_callback'] )
		) {
			$setting['sanitize_callback'] = null;
		}

		if (
			'' === $setting['id'] ||
			'' === $setting['label'] ||
			'' === $setting['type']
		) {
			return array();
		}

		return $setting;
	}

	/**
	 * Sanitize a single registered choice.
	 *
	 * @param mixed $value   Raw choice value.
	 * @param array $setting Registered setting definition.
	 *
	 * @return mixed
	 */
	private function sanitize_choice(
		$value,
		array $setting ) {

		$choices = isset( $setting['choices'] )
			&& is_array( $setting['choices'] )
				? $setting['choices']
				: array();

		$choice_key = sanitize_key(
			(string) $value
		);

		return array_key_exists( $choice_key, $choices )
			? $choice_key
			: $setting['default'];
	}

	/**
	 * Sanitize multiple registered choices.
	 *
	 * Settings without fixed choices retain sanitized identifiers. This is
	 * useful for dynamic collections such as dashboard panel IDs.
	 *
	 * @param mixed $values  Raw choice values.
	 * @param array $setting Registered setting definition.
	 *
	 * @return array
	 */
	private function sanitize_multiple_choices(
		$values,
		array $setting ) {

		if ( ! is_array( $values ) ) {
			return array();
		}

		$values = array_values(
			array_unique(
				array_filter(
					array_map(
						'sanitize_key',
						$values
					)
				)
			)
		);

		$choices = isset( $setting['choices'] )
			&& is_array( $setting['choices'] )
				? $setting['choices']
				: array();

		if ( empty( $choices ) ) {
			return $values;
		}

		return array_values(
			array_filter(
				$values,
				static function ( $value ) use ( $choices ) {
					return array_key_exists(
						$value,
						$choices
					);
				}
			)
		);
	}

	/**
	 * Sanitize a namespaced setting identifier.
	 *
	 * Dot notation is preserved so identifiers may use structures such as
	 * dashboard.density or appearance.accent_color.
	 *
	 * @param mixed $setting_id Raw setting identifier.
	 *
	 * @return string
	 */
	private function sanitize_setting_id( $setting_id ) {

		$parts = explode(
			'.',
			strtolower(
				trim(
					(string) $setting_id
				)
			)
		);

		$parts = array_map(
			'sanitize_key',
			$parts
		);

		$parts = array_filter(
			$parts,
			static function ( $part ) {
				return '' !== $part;
			}
		);

		return implode(
			'.',
			$parts
		);
	}

	/**
	 * Normalize a setting field type.
	 *
	 * @param mixed $type Raw setting type.
	 *
	 * @return string
	 */
	private function normalize_type( $type ) {

		$type = sanitize_key(
			(string) $type
		);

		$allowed_types = array(
			'checkbox',
			'color',
			'multiselect',
			'number',
			'select',
			'text',
			'textarea',
		);

		return in_array(
			$type,
			$allowed_types,
			true
		)
			? $type
			: 'text';
	}

	/**
	 * Normalize a setting storage scope.
	 *
	 * @param mixed $scope Raw storage scope.
	 *
	 * @return string
	 */
	private function normalize_scope( $scope ) {

		$scope = sanitize_key(
			(string) $scope
		);

		$allowed_scopes = array(
			'user',
			'site',
		);

		return in_array(
			$scope,
			$allowed_scopes,
			true
		)
			? $scope
			: 'user';
	}
}