<?php
/**
 * Compact dashboard header.
 *
 * Expected variables:
 *
 * @var string $site_name Site name.
 * @var array  $user      Current-user information.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

use function GOUG\Inc\Helpers\get_brand_icon_url;

$site_name = isset( $site_name )
	? (string) $site_name
	: '';

$user = isset( $user ) && is_array( $user )
	? $user
	: array();

$display_name = isset( $user['display_name'] )
	? (string) $user['display_name']
	: '';

$greeting = isset( $user['greeting'] )
	? (string) $user['greeting']
	: __( 'Welcome', 'goug-framework' );

$current_date = isset( $user['date'] )
	? (string) $user['date']
	: '';

$avatar_url = isset( $user['avatar_url'] )
	? (string) $user['avatar_url']
	: '';

$logo_url = get_brand_icon_url( 96 );
?>

<div class="goug-dashboard-topbar">

	<a
		class="goug-dashboard-topbar__brand"
		href="<?php echo esc_url( home_url( '/' ) ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<?php if ( '' !== $logo_url ) : ?>
			<img
				class="goug-dashboard-topbar__logo"
				src="<?php echo esc_url( $logo_url ); ?>"
				alt="<?php
				echo esc_attr(
					sprintf(
						/* translators: %s: Site name. */
						__( '%s site icon', 'goug-framework' ),
						$site_name
					)
				);
				?>"
			>
		<?php endif; ?>

		<span class="goug-dashboard-topbar__site-name">
			<?php echo esc_html( $site_name ); ?>
		</span>
	</a>

	<form
		class="goug-dashboard-search"
		method="get"
		action="<?php echo esc_url( admin_url( 'edit.php' ) ); ?>"
		role="search"
	>
		<label
			class="screen-reader-text"
			for="goug-dashboard-search"
		>
			<?php esc_html_e( 'Search posts', 'goug-framework' ); ?>
		</label>

		<span
			class="goug-dashboard-search__icon dashicons dashicons-search"
			aria-hidden="true"
		></span>

		<input
			id="goug-dashboard-search"
			class="goug-dashboard-search__input"
			type="search"
			name="s"
			placeholder="<?php
			echo esc_attr__(
				'Search posts…',
				'goug-framework'
			);
			?>"
			autocomplete="off"
		>

		<button
			class="goug-dashboard-search__button"
			type="submit"
		>
			<?php esc_html_e( 'Search', 'goug-framework' ); ?>
		</button>
	</form>

	<div class="goug-dashboard-user">

		<div class="goug-dashboard-user__details">
			<strong class="goug-dashboard-user__greeting">
				<?php
				printf(
					/* translators: 1: Greeting. 2: User display name. */
					esc_html__( '%1$s, %2$s', 'goug-framework' ),
					esc_html( $greeting ),
					esc_html( $display_name )
				);
				?>
			</strong>

			<?php if ( '' !== $current_date ) : ?>
				<span class="goug-dashboard-user__date">
					<?php echo esc_html( $current_date ); ?>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( '' !== $avatar_url ) : ?>
			<img
				class="goug-dashboard-user__avatar"
				src="<?php echo esc_url( $avatar_url ); ?>"
				alt="<?php
				echo esc_attr(
					sprintf(
						/* translators: %s: User display name. */
						__( 'Avatar for %s', 'goug-framework' ),
						$display_name
					)
				);
				?>"
			>
		<?php endif; ?>

	</div>

</div>

<section class="goug-dashboard-intro">

	<div>
		<h1 class="goug-dashboard-intro__title">
			<?php esc_html_e( 'Dashboard', 'goug-framework' ); ?>
		</h1>

		<p class="goug-dashboard-intro__description">
			<?php
			printf(
				/* translators: %s: Site name. */
				esc_html__(
					'Here’s what’s happening with %s.',
					'goug-framework'
				),
				esc_html( $site_name )
			);
			?>
		</p>
	</div>

	<a
		class="goug-dashboard-intro__site-link"
		href="<?php echo esc_url( home_url( '/' ) ); ?>"
		target="_blank"
		rel="noopener noreferrer"
	>
		<span
			class="dashicons dashicons-external"
			aria-hidden="true"
		></span>

		<?php esc_html_e( 'View Site', 'goug-framework' ); ?>
	</a>

</section>