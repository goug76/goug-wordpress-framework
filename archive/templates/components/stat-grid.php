<?php
/**
 * Reusable dashboard stat grid.
 *
 * Expected variables:
 *
 * @var array  $items      Stat items.
 * @var string $class_name Optional additional grid classes.
 *
 * Each item may contain:
 *
 * - label
 * - value
 * - description
 * - icon
 * - state
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$items = isset( $items ) && is_array( $items )
	? $items
	: array();

$class_name = isset( $class_name )
	? (string) $class_name
	: '';

if ( empty( $items ) ) {
	return;
}

$grid_classes = array(
	'goug-stat-grid',
);

if ( '' !== $class_name ) {
	$grid_classes[] = $class_name;
}

$grid_class_attribute = implode(
	' ',
	array_map(
		'sanitize_html_class',
		$grid_classes
	)
);
?>

<div class="<?php echo esc_attr( $grid_class_attribute ); ?>">

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

		$description = isset( $item['description'] )
			? (string) $item['description']
			: '';

		$icon = isset( $item['icon'] )
			? sanitize_html_class( $item['icon'] )
			: '';

		$state = isset( $item['state'] )
			? sanitize_html_class( $item['state'] )
			: 'default';
		?>

		<div
			class="goug-stat-item goug-stat-item--<?php
			echo esc_attr( $state );
			?>"
		>
			<?php if ( '' !== $icon ) : ?>
				<span
					class="goug-stat-item__icon dashicons <?php
					echo esc_attr( $icon );
					?>"
					aria-hidden="true"
				></span>
			<?php endif; ?>

			<div class="goug-stat-item__content">

				<span class="goug-stat-item__label">
					<?php echo esc_html( $label ); ?>
				</span>

				<strong class="goug-stat-item__value">
					<?php echo esc_html( $value ); ?>
				</strong>

				<?php if ( '' !== $description ) : ?>
					<span class="goug-stat-item__description">
						<?php echo esc_html( $description ); ?>
					</span>
				<?php endif; ?>

			</div>
		</div>

	<?php endforeach; ?>

</div>