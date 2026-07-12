<?php
/**
 * Reusable framework card component.
 *
 * Expected variables:
 *
 * @var string $title       Card title.
 * @var string $icon        Dashicons class.
 * @var string $url         Optional destination URL.
 * @var string $description Optional supporting text.
 * @var string $badge       Optional badge text.
 * @var string $class_name  Optional additional CSS classes.
 * @var string $target      Optional link target.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$title       = isset( $title ) ? (string) $title : '';
$icon        = isset( $icon ) ? (string) $icon : '';
$url         = isset( $url ) ? (string) $url : '';
$description = isset( $description ) ? (string) $description : '';
$badge       = isset( $badge ) ? (string) $badge : '';
$class_name  = isset( $class_name ) ? (string) $class_name : '';
$target      = isset( $target ) ? (string) $target : '';

$classes = array(
	'goug-card',
	'goug-card--action',
);

if ( '' !== $class_name ) {
	$additional_classes = preg_split(
		'/\s+/',
		$class_name,
		-1,
		PREG_SPLIT_NO_EMPTY
	);

	$classes = array_merge( $classes, $additional_classes );
}

$class_attribute = implode(
	' ',
	array_map( 'sanitize_html_class', $classes )
);

$link_attributes = '';

if ( '_blank' === $target ) {
	$link_attributes = ' target="_blank" rel="noopener noreferrer"';
}
?>

<?php if ( '' !== $url ) : ?>

	<a
		class="<?php echo esc_attr( $class_attribute ); ?>"
		href="<?php echo esc_url( $url ); ?>"
		<?php echo $link_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	>

<?php else : ?>

	<div class="<?php echo esc_attr( $class_attribute ); ?>">

<?php endif; ?>

	<?php if ( '' !== $badge ) : ?>
		<span class="goug-card__badge">
			<?php echo esc_html( $badge ); ?>
		</span>
	<?php endif; ?>

	<?php if ( '' !== $icon ) : ?>
		<span
			class="goug-card__icon dashicons <?php echo esc_attr(
				sanitize_html_class( $icon )
			); ?>"
			aria-hidden="true"
		></span>
	<?php endif; ?>

	<div class="goug-card__content">

		<?php if ( '' !== $title ) : ?>
			<span class="goug-card__title">
				<?php echo esc_html( $title ); ?>
			</span>
		<?php endif; ?>

		<?php if ( '' !== $description ) : ?>
			<span class="goug-card__description">
				<?php echo esc_html( $description ); ?>
			</span>
		<?php endif; ?>

	</div>

<?php if ( '' !== $url ) : ?>

	</a>

<?php else : ?>

	</div>

<?php endif; ?>