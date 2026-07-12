<?php
/**
 * Framework helper functions.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Helpers;

defined( 'ABSPATH' ) || exit;

/**
 * Return the current site branding logo URL.
 *
 * Uses the WordPress Custom Logo when one is configured. Falls back
 * to the bundled Goug Labs logo when the site has no custom logo.
 *
 * @param string $size Registered WordPress image size.
 *
 * @return string
 */
function get_brand_logo_url( $size = 'full' ) {

	$custom_logo_id = (int) get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id > 0 ) {
		$custom_logo_url = wp_get_attachment_image_url(
			$custom_logo_id,
			$size
		);

		if ( $custom_logo_url ) {
			return $custom_logo_url;
		}
	}

	return get_stylesheet_directory_uri()
		. '/assets/images/gouglabs_logo_dark.webp';
}