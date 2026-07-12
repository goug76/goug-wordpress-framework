<?php
/**
 * Dashboard action group component.
 *
 * Expected variables:
 *
 * @var array  $group      Action group containing a title and item array.
 * @var string $class_name Optional additional wrapper classes.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$group      = isset( $group ) && is_array( $group )
	? $group
	: array();

$class_name = isset( $class_name )
	? (string) $class_name
	: '';

$title = isset( $group['title'] )
	? (string) $group['title']
	: '';

$items = isset( $group['items'] ) && is_array( $group['items'] )
	? $group['items']
	: array();

/*
 * Do not render an empty action group.
 */
if ( empty( $items ) ) {
	return;
}

$classes = array(
	'goug-action-group',
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
		<h3 class="goug-action-group__title">
			<?php echo esc_html( $title ); ?>
		</h3>
	<?php endif; ?>

	<div class="goug-action-group__grid goug-flex goug-card-grid">

		<?php foreach ( $items as $action ) : ?>

			<?php
			if ( ! is_array( $action ) ) {
				continue;
			}

			\GOUG\Inc\View::render(
				'components/card',
				array(
					'title'       => isset( $action['title'] )
						? $action['title']
						: '',
					'icon'        => isset( $action['icon'] )
						? $action['icon']
						: '',
					'url'         => isset( $action['url'] )
						? $action['url']
						: '',
					'description' => isset( $action['description'] )
						? $action['description']
						: '',
					'badge'       => isset( $action['badge'] )
						? $action['badge']
						: '',
					'target'      => isset( $action['target'] )
						? $action['target']
						: '',
					'class_name'  => 'goug-actions',
				)
			);
			?>

		<?php endforeach; ?>

	</div>

</section>