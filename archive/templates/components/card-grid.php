<?php
/**
 * Reusable card grid component.
 *
 * Expected variables:
 *
 * @var array  $items      Cards to render.
 * @var string $class_name Optional additional grid classes.
 * @var string $card_class Optional classes passed to each card.
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

$card_class = isset( $card_class )
	? (string) $card_class
	: '';

if ( empty( $items ) ) {
	return;
}

$classes = array(
	'goug-card-grid',
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

<div class="<?php echo esc_attr( $class_attribute ); ?>">

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
				'target'      => isset( $item['target'] )
					? $item['target']
					: '',
				'class_name'  => $card_class,
			)
		);
		?>

	<?php endforeach; ?>

</div>