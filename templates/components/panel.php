<?php
/**
 * Reusable framework panel component.
 *
 * Expected variables:
 *
 * @var string $panel_id	Panel ID
 * @var string $title       Optional panel title.
 * @var string $icon        Optional Dashicons class.
 * @var string $body_view   View rendered inside the panel body.
 * @var array  $body_data   Data passed to the body view.
 * @var string $class_name  Optional additional wrapper classes.
 * @var array  $attributes  Optional HTML data and ARIA attributes.
 * @var bool   $collapsed   Whether the panel is initially collapsed.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$title      = isset( $title ) ? (string) $title : '';
$icon       = isset( $icon ) ? (string) $icon : '';
$body_view  = isset( $body_view ) ? (string) $body_view : '';
$body_data  = isset( $body_data ) && is_array( $body_data )
	? $body_data
	: array();
$attributes = isset( $attributes ) && is_array( $attributes )
	? $attributes
	: array();
$class_name = isset( $class_name ) ? (string) $class_name : '';
$collapsed 	= ! empty( $collapsed );

if ( '' === $body_view ) {
	return;
}

$classes = array(
	'goug-panel',
);

if ( $collapsed ) {
    $classes[] = 'is-collapsed';
}

if ( '' !== $class_name ) {
	$additional_classes = preg_split(
		'/\s+/',
		$class_name,
		-1,
		PREG_SPLIT_NO_EMPTY
	);

	if ( is_array( $additional_classes ) ) {
		$classes = array_merge(
			$classes,
			$additional_classes
		);
	}
}

$class_attribute = implode(
	' ',
	array_map(
		'sanitize_html_class',
		$classes
	)
);

$attribute_parts = array();

foreach ( $attributes as $attribute_name => $attribute_value ) {

	$attribute_name = strtolower(
		preg_replace(
			'/[^a-zA-Z0-9_\-:]/',
			'',
			(string) $attribute_name
		)
	);

	if ( '' === $attribute_name ) {
		continue;
	}

	/*
	 * Boolean true renders the standalone attribute.
	 * False and null omit the attribute.
	 */
	if ( true === $attribute_value ) {
		$attribute_parts[] = esc_attr( $attribute_name );
		continue;
	}

	if (
		false === $attribute_value ||
		null === $attribute_value
	) {
		continue;
	}

	$attribute_parts[] = sprintf(
		'%1$s="%2$s"',
		esc_attr( $attribute_name ),
		esc_attr( (string) $attribute_value )
	);
}

$attribute_string = implode( ' ', $attribute_parts );
?>

<section
	class="<?php echo esc_attr( $class_attribute ); ?>"
	<?php
	echo $attribute_string; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	?>
>

	<?php if ( '' !== $title || '' !== $icon ) : ?>
		<header class="goug-panel__header">

			<?php
			$icon = isset( $icon )
				? sanitize_html_class( $icon )
				: '';

			$icon_svg = isset( $icon_svg )
				? sanitize_file_name( $icon_svg )
				: '';

			$panel_svg_url = '' !== $icon_svg
				? \GOUG\Inc\Helpers\get_icon_url( $icon_svg )
				: '';
			?>
			
			<?php
			echo \GOUG\Inc\Helpers\get_drag_handle_markup(
				__(
					'Reorder panel',
					'goug-framework'
				)
			); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>

			<?php if (
				'' !== $panel_svg_url ||
				'' !== $icon
			) : ?>

				<span class="goug-panel__icon" aria-hidden="true">

					<?php if ( '' !== $panel_svg_url ) : ?>

						<span
							class="goug-svg-icon"
							style="<?php
							echo esc_attr(
								'--goug-icon-url: url('
								. esc_url_raw( $panel_svg_url )
								. ');'
							);
							?>"
						></span>

					<?php else : ?>

						<span
							class="dashicons <?php
							echo esc_attr( $icon );
							?>"
						></span>

					<?php endif; ?>

				</span>

			<?php endif; ?>

			<?php if ( '' !== $title ) : ?>
				<h3 class="goug-panel__title">
					<?php echo esc_html( $title ); ?>
				</h3>
			<?php endif; ?>

			<button
				class="goug-panel__toggle"
				type="button"
				aria-expanded="<?php echo $collapsed ? 'false' : 'true'; ?>"
				aria-label="<?php
					echo esc_attr(
						$collapsed
							? __( 'Expand panel', 'goug-framework' )
							: __( 'Collapse panel', 'goug-framework' )
					);
				?>"
			>
				<span
					class="dashicons dashicons-arrow-down-alt2"
					aria-hidden="true">
				</span>
			</button>

		</header>
	<?php endif; ?>	

	<div class="goug-panel__body">
		<?php
		\GOUG\Inc\View::render(
			$body_view,
			$body_data
		);
		?>
	</div>

</section>