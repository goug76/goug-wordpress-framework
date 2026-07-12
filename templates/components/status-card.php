<?php
/**
 * Reusable status card component.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$label = isset( $label ) ? (string) $label : '';
$value = isset( $value ) ? (string) $value : '';
$meta  = isset( $meta ) ? (string) $meta : '';
$icon  = isset( $icon ) ? (string) $icon : '';
$state = isset( $state )
	? sanitize_html_class( $state )
	: 'neutral';
$url   = isset( $url ) ? (string) $url : '';

$classes = array(
	'goug-status-card',
	'goug-status-card--' . $state,
);
?>

<?php if ( '' !== $url ) : ?>

	<a
		class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>"
		href="<?php echo esc_url( $url ); ?>"
	>

<?php else : ?>

	<div class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">

<?php endif; ?>

	<div class="goug-status-card__icon-wrap">
		<span
			class="goug-status-card__icon dashicons <?php echo esc_attr(
				sanitize_html_class( $icon )
			); ?>"
			aria-hidden="true"
		></span>
	</div>

	<div class="goug-status-card__content">

		<?php if ( '' !== $label ) : ?>
			<span class="goug-status-card__label">
				<?php echo esc_html( $label ); ?>
			</span>
		<?php endif; ?>

		<?php if ( '' !== $value ) : ?>
			<strong class="goug-status-card__value">
				<?php echo esc_html( $value ); ?>
			</strong>
		<?php endif; ?>

		<?php if ( '' !== $meta ) : ?>
			<span class="goug-status-card__meta">
				<?php echo esc_html( $meta ); ?>
			</span>
		<?php endif; ?>

	</div>

<?php if ( '' !== $url ) : ?>

	</a>

<?php else : ?>

	</div>

<?php endif; ?>