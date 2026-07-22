<?php
/**
 * Dashboard Site Status grid.
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

<div class="goug-status-grid">

	<?php foreach ( $items as $item ) : ?>

		<?php
		if ( ! is_array( $item ) ) {
			continue;
		}

		\GOUG\Inc\View::render(
			'components/status-card',
			array(
				'label' => isset( $item['label'] )
					? $item['label']
					: '',
				'value' => isset( $item['value'] )
					? $item['value']
					: '',
				'meta'  => isset( $item['meta'] )
					? $item['meta']
					: '',
				'icon'  => isset( $item['icon'] )
					? $item['icon']
					: '',
				'state' => isset( $item['state'] )
					? $item['state']
					: 'neutral',
				'url'   => isset( $item['url'] )
					? $item['url']
					: '',
			)
		);
		?>

	<?php endforeach; ?>

</div>