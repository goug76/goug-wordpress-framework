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

/**
 * Return the current WordPress Site Icon URL.
 *
 * Returns an empty string when no Site Icon is configured.
 *
 * @param int $size Requested square image size.
 *
 * @return string
 */
function get_brand_icon_url( $size = 96 ) {

	$site_icon_url = get_site_icon_url( absint( $size ) );

	return $site_icon_url
		? $site_icon_url
		: '';
}

/**
 * Return the URL for a bundled framework icon.
 *
 * @param string $filename Icon filename.
 *
 * @return string
 */
function get_icon_url( $filename ) {

	$filename = basename( (string) $filename );

	if ( '' === $filename ) {
		return '';
	}

	return get_stylesheet_directory_uri()
		. '/assets/icons/'
		. rawurlencode( $filename );
}

/**
 * Return reusable drag-handle markup.
 *
 * @param string $label Accessible control label.
 *
 * @return string
 */
function get_drag_handle_markup(
	$label = ''
) {

	if ( '' === $label ) {
		$label = __(
			'Reorder item',
			'goug-framework'
		);
	}

	ob_start();
	?>

	<span
		class="goug-drag-handle"
		role="button"
		tabindex="0"
		aria-label="<?php echo esc_attr( $label ); ?>"
	>
		<span
			class="goug-drag-handle__grip"
			aria-hidden="true"
		>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
			<span></span>
		</span>
	</span>

	<?php

	return trim(
		ob_get_clean()
	);
}