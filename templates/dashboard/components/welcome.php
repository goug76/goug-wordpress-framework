<?php
/**
 * Dashboard Welcome panel content.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$site_name = isset( $site_name )
	? (string) $site_name
	: get_bloginfo( 'name' );

$description = isset( $description )
	? (string) $description
	: '';

$profile_url = isset( $profile_url )
	? (string) $profile_url
	: admin_url( 'profile.php' );
?>

<div class="goug-welcome">

	<div class="goug-welcome__content">

		<h2 class="goug-welcome__title">
			<?php
			echo esc_html(
				sprintf(
					/* translators: %s: Site name. */
					__( 'Welcome to %s', 'goug-framework' ),
					$site_name
				)
			);
			?>
		</h2>

		<p class="goug-welcome__message">
			<?php
			echo esc_html__(
				'Your dashboard shows tools based on your account permissions.',
				'goug-framework'
			);
			?>
		</p>

		<?php if ( '' !== $description ) : ?>

			<p class="goug-welcome__description">
				<?php echo esc_html( $description ); ?>
			</p>

		<?php endif; ?>

		<a
			class="goug-dashboard-intro__site-link goug-welcome__profile-link"
			href="<?php echo esc_url( $profile_url ); ?>"
		>
			<?php esc_html_e( 'Edit Profile', 'goug-framework' ); ?>
		</a>

	</div>

</div>