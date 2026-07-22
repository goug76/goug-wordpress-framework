<?php
/**
 * Dashboard At a Glance component.
 *
 * @var array $items Prepared dashboard statistics.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$items = isset( $items ) && is_array( $items )
	? $items
	: array();

if ( empty( $items ) ) {
	return;
}
?>

<div class="goug-glance-list">

	<?php foreach ( $items as $item ) : ?>

		<?php
		if (
			! is_array( $item ) ||
			empty( $item['label'] )
		) {
			continue;
		}

		$label = (string) $item['label'];

		$value = isset( $item['value'] )
			? (string) $item['value']
			: '0';

		$icon = isset( $item['icon'] )
			? sanitize_html_class( $item['icon'] )
			: '';

		$secondary = isset( $item['secondary'] )
			? (string) $item['secondary']
			: '';

		$state = isset( $item['state'] )
			? sanitize_html_class( $item['state'] )
			: 'default';
		?>

		<div
			class="goug-glance-item goug-glance-item--<?php
			echo esc_attr( $state );
			?>"
		>
			<div class="goug-glance-item__label">

				<?php if ( '' !== $icon ) : ?>
					<span
						class="goug-glance-item__icon dashicons <?php
						echo esc_attr( $icon );
						?>"
						aria-hidden="true"
					></span>
				<?php endif; ?>

				<span>
					<?php echo esc_html( $label ); ?>
				</span>

			</div>

			<div class="goug-glance-item__values">

				<strong class="goug-glance-item__value">
					<?php echo esc_html( $value ); ?>
				</strong>

				<?php if ( '' !== $secondary ) : ?>
					<span class="goug-glance-item__secondary">
						<?php echo esc_html( $secondary ); ?>
					</span>
				<?php endif; ?>

			</div>
		</div>

	<?php endforeach; ?>

</div>