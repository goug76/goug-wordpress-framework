<?php
/**
 * View renderer.
 *
 * Resolves and renders framework template files using prepared data.
 *
 * @package GOUG
 */

namespace GOUG\Inc;

defined( 'ABSPATH' ) || exit;

class View {

	/**
	 * Render a framework template.
	 *
	 * @param string $template  Relative template path without the .php extension.
	 * @param array  $view_data Data made available to the template.
	 *
	 * @return void
	 */
	public static function render( $template, array $view_data = array() ) {

		$template_path = self::get_template_path( $template );

		if ( ! $template_path ) {
			return;
		}

		/*
		* Make array keys available as local variables inside the template.
		*
		* EXTR_SKIP prevents view data from overwriting variables already
		* defined inside this renderer.
		*/
		if ( ! empty( $view_data ) ) {
			extract( $view_data, EXTR_SKIP );
		}

		require $template_path;
	}

	/**
	 * Resolve and validate a framework template path.
	 *
	 * @param string $template Relative template path without .php.
	 *
	 * @return string|false
	 */
	private static function get_template_path( $template ) {

		$template = trim( (string) $template );

		if ( '' === $template ) {
			return false;
		}

		/*
		 * Normalize slashes and remove a supplied .php extension.
		 */
		$template = str_replace( '\\', '/', $template );
		$template = preg_replace( '/\.php$/', '', $template );
		$template = trim( $template, '/' );

		/*
		 * Reject directory traversal and invalid paths.
		 */
		if (
			'' === $template ||
			false !== strpos( $template, '..' ) ||
			0 !== validate_file( $template )
		) {
			return false;
		}

		$template_root = trailingslashit(
			get_stylesheet_directory() . '/templates'
		);

		$template_path = $template_root . $template . '.php';

		if ( ! file_exists( $template_path ) ) {
			return false;
		}

		return $template_path;
	}
}