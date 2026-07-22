<?php
/**
 * Autoloader for theme classes, traits, widgets, and blocks.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Autoload resources within the GOUG namespace.
 *
 * Examples:
 *
 * GOUG\Inc\Dashboard
 * → inc/classes/class-dashboard.php
 *
 * GOUG\Inc\Dashboard\Dashboard_Data
 * → inc/classes/dashboard/class-dashboard-data.php
 *
 * GOUG\Inc\Traits\Singleton
 * → inc/traits/trait-singleton.php
 *
 * GOUG\Inc\Widgets\Example_Widget
 * → inc/classes/widgets/class-example-widget.php
 *
 * @param string $resource Fully qualified class, trait, or interface name.
 *
 * @return void
 */
function autoloader( $resource = '' ) {

	$namespace_root = 'GOUG\\';
	$resource       = trim( $resource, '\\' );

	/*
	 * Ignore empty resources and anything outside our namespace.
	 */
	if (
		empty( $resource ) ||
		0 !== strpos( $resource, $namespace_root )
	) {
		return;
	}

	/*
	 * Remove the root GOUG namespace.
	 *
	 * GOUG\Inc\Dashboard\Dashboard_Data
	 * becomes:
	 * Inc\Dashboard\Dashboard_Data
	 */
	$resource = substr( $resource, strlen( $namespace_root ) );

	/*
	 * Convert namespace segments into WordPress-style paths.
	 *
	 * Dashboard_Data becomes dashboard-data.
	 */
	$path = array_map(
		static function ( $segment ) {
			return str_replace( '_', '-', strtolower( $segment ) );
		},
		explode( '\\', $resource )
	);

	if (
		empty( $path[0] ) ||
		'inc' !== $path[0] ||
		empty( $path[1] )
	) {
		return;
	}

	$directory = '';
	$file_name = '';

	switch ( $path[1] ) {

		case 'traits':
			if ( empty( $path[2] ) ) {
				return;
			}

			$directory = 'traits';
			$file_name = sprintf(
				'trait-%s.php',
				$path[2]
			);
			break;

		case 'widgets':
		case 'blocks':
			if ( empty( $path[2] ) ) {
				return;
			}

			$directory = sprintf(
				'classes/%s',
				$path[1]
			);

			$file_name = sprintf(
				'class-%s.php',
				$path[2]
			);
			break;

		default:
			/*
			 * Top-level class:
			 *
			 * GOUG\Inc\Dashboard
			 * → inc/classes/class-dashboard.php
			 */
			if ( 2 === count( $path ) ) {
				$directory = 'classes';
				$file_name = sprintf(
					'class-%s.php',
					$path[1]
				);
				break;
			}

			/*
			 * Nested class:
			 *
			 * GOUG\Inc\Dashboard\Dashboard_Data
			 * → inc/classes/dashboard/class-dashboard-data.php
			 *
			 * Everything between "Inc" and the final class name becomes
			 * part of the directory path.
			 */
			$directory_segments = array_slice( $path, 1, -1 );
			$class_name         = end( $path );

			$directory = sprintf(
				'classes/%s',
				implode( '/', $directory_segments )
			);

			$file_name = sprintf(
				'class-%s.php',
				$class_name
			);
			break;
	}

	if ( empty( $directory ) || empty( $file_name ) ) {
		return;
	}

	$resource_path = sprintf(
		'%s/inc/%s/%s',
		untrailingslashit( GOUG_DIR_PATH ),
		$directory,
		$file_name
	);

	/*
	 * WordPress returns:
	 *
	 * 0: Valid path.
	 * 1: Directory traversal detected.
	 * 2: Windows drive path.
	 */
	$is_valid_file = validate_file( $resource_path );

	if (
		file_exists( $resource_path ) &&
		in_array( $is_valid_file, array( 0, 2 ), true )
	) {
		require_once $resource_path;
	}
}

spl_autoload_register( __NAMESPACE__ . '\\autoloader' );