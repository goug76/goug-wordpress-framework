<?php
/**
 * Dashboard Site Overview component.
 *
 * Expected variables:
 *
 * @var array  $overview   Overview title and card items.
 * @var string $class_name Optional wrapper classes.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$overview = isset( $overview ) && is_array( $overview )
	? $overview
	: array();

$class_name = isset( $class_name )
	? (string) $class_name
	: '';

$title = isset( $overview['title'] )
	? (string) $overview['title']
	: '';

$items = isset( $overview['items'] ) && is_array( $overview['items'] )
	? $overview['items']
	: array();

if ( empty( $items ) ) {
	return;
}

$classes = array(
	'goug-site-overview',
);

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
?>

<section class="<?php echo esc_attr( $class_attribute ); ?>">

	<?php if ( '' !== $title ) : ?>
		<h3 class="goug-site-overview__title">
			<?php echo esc_html( $title ); ?>
		</h3>
	<?php endif; ?>

	<div class="goug-site-overview__grid goug-flex goug-card-grid">

		<?php foreach ( $items as $item ) : ?>

			<?php
			if ( ! is_array( $item ) ) {
				continue;
			}

			\GOUG\Inc\View::render(
				'components/card',
				array(
					'title'       => isset( $item['title'] )
						? $item['title']
						: '',
					'icon'        => isset( $item['icon'] )
						? $item['icon']
						: '',
					'url'         => isset( $item['url'] )
						? $item['url']
						: '',
					'description' => isset( $item['description'] )
						? $item['description']
						: '',
					'badge'       => isset( $item['badge'] )
						? $item['badge']
						: '',
					'class_name'  => 'goug-actions goug-stats',
				)
			);
			?>

		<?php endforeach; ?>

	</div>

</section>