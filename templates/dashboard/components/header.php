<?php
/**
 * Dashboard header component.
 *
 * Expected variables:
 *
 * @var string $user_name Display name of the current user.
 * @var string $site_name Website name.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

use function GOUG\Inc\Helpers\get_brand_logo_url;

$logo_url = get_brand_logo_url();
?>

<header class="goug-container goug-conainer goug-dashboard-header">
	<a
		href="<?php echo esc_url( GOUG_LAB_URL ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<img
			src="<?php echo esc_url( $logo_url ); ?>"
			alt="<?php
			echo esc_attr(
				sprintf(
					/* translators: %s: Site name. */
					__( '%s logo', 'goug-framework' ),
					$site_name
				)
			);
			?>"
		>
	</a>

	<h1>
		<?php
		printf(
			/* translators: 1: User display name. 2: Site name. */
			esc_html__(
				'Hi %1$s, welcome to the %2$s Dashboard',
				'goug-framework'
			),
			esc_html( $user_name ),
			esc_html( $site_name )
		);
		?>
	</h1>

	<p>
		<?php
		esc_html_e(
			'This is your personalized admin area where you can manage everything at a glance.',
			'goug-framework'
		);
		?>
	</p>
</header>