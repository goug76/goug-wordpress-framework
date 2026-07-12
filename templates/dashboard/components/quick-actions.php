<?php
/**
 * Dashboard Quick Actions component.
 *
 * Expected variables:
 *
 * @var array $groups Action groups containing titles and items.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$groups = isset( $groups ) && is_array( $groups )
	? $groups
	: array();

if ( empty( $groups ) ) {
	return;
}
?>

<div class="goug-quick-actions">

	<?php foreach ( $groups as $group ) : ?>

		<?php
		if (
			! is_array( $group ) ||
			empty( $group['items'] ) ||
			! is_array( $group['items'] )
		) {
			continue;
		}

		$group_title = isset( $group['title'] )
			? (string) $group['title']
			: '';
		?>

		<section class="goug-quick-actions__group">

			<?php if ( '' !== $group_title ) : ?>
				<h4 class="goug-quick-actions__group-title">
					<?php echo esc_html( $group_title ); ?>
				</h4>
			<?php endif; ?>

			<?php
			\GOUG\Inc\View::render(
				'components/card-grid',
				array(
					'items'      => $group['items'],
					'class_name' => 'goug-card-grid--quick-actions',
					'card_class' => 'goug-card--compact',
				)
			);
			?>

		</section>

	<?php endforeach; ?>

</div>