<?php
/**
 * Dashboard System Updates component.
 *
 * Expected variables:
 *
 * @var array $system_updates System Updates title and items.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$system_updates = isset( $system_updates )
	&& is_array( $system_updates )
		? $system_updates
		: array();

$title = isset( $system_updates['title'] )
	? (string) $system_updates['title']
	: '';

$items = isset( $system_updates['items'] )
	&& is_array( $system_updates['items'] )
		? $system_updates['items']
		: array();

if ( empty( $items ) ) {
	return;
}
?>

<section class="goug-updates">

	<?php if ( '' !== $title ) : ?>
		<h3 class="goug-updates__title">
			<?php echo esc_html( $title ); ?>
		</h3>
	<?php endif; ?>

	<div class="goug-updates__grid goug-flex">

		<?php foreach ( $items as $item ) : ?>

			<?php
			if ( ! is_array( $item ) ) {
				continue;
			}

			$item_title = isset( $item['title'] )
				? (string) $item['title']
				: '';

			$icon = isset( $item['icon'] )
				? (string) $item['icon']
				: '';

			$url = isset( $item['url'] )
				? (string) $item['url']
				: '';

			$status = isset( $item['status'] )
				? (string) $item['status']
				: '';

			$state = isset( $item['state'] )
				? sanitize_html_class( $item['state'] )
				: 'neutral';
			?>

			<a
				class="goug-update-item"
				href="<?php echo esc_url( $url ); ?>"
			>
				<?php if ( '' !== $icon ) : ?>
					<span
						class="dashicons <?php echo esc_attr(
							sanitize_html_class( $icon )
						); ?>"
						aria-hidden="true"
					></span>
				<?php endif; ?>

				<?php if ( '' !== $item_title ) : ?>
					<p class="goug-update-item__title">
						<?php echo esc_html( $item_title ); ?>
					</p>
				<?php endif; ?>

				<?php if ( '' !== $status ) : ?>
					<span
						class="status <?php echo esc_attr( $state ); ?>"
					>
						<?php echo esc_html( $status ); ?>
					</span>
				<?php endif; ?>
			</a>

		<?php endforeach; ?>

	</div>

</section>