<?php
/**
 * Dashboard Site Health component.
 *
 * @var array $health Site Health summary.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$health = isset( $health ) && is_array( $health )
	? $health
	: array();

if ( empty( $health ) ) {
	return;
}

$status = isset( $health['status'] )
	? sanitize_html_class( $health['status'] )
	: 'good';

$stats = array(
	array(
		'label'       => __( 'Critical', 'goug-framework' ),
		'value'       => $health['critical'] ?? 0,
		'description' => __( 'Issues requiring action', 'goug-framework' ),
		'icon'        => 'dashicons-warning',
		'state'       => ! empty( $health['critical'] )
			? 'error'
			: 'success',
	),
	array(
		'label'       => __( 'Recommended', 'goug-framework' ),
		'value'       => $health['recommended'] ?? 0,
		'description' => __( 'Suggested improvements', 'goug-framework' ),
		'icon'        => 'dashicons-info-outline',
		'state'       => ! empty( $health['recommended'] )
			? 'warning'
			: 'success',
	),
	array(
		'label'       => __( 'Passed', 'goug-framework' ),
		'value'       => $health['passed'] ?? 0,
		'description' => __( 'Checks completed successfully', 'goug-framework' ),
		'icon'        => 'dashicons-yes-alt',
		'state'       => 'success',
	),
);
?>

<div class="goug-health-summary goug-health-summary--<?php echo esc_attr( $status ); ?>">

	<div class="goug-health-summary__overview">
		<span class="goug-health-summary__indicator" aria-hidden="true"></span>

		<div>
			<strong class="goug-health-summary__label">
				<?php echo esc_html( $health['label'] ?? '' ); ?>
			</strong>

			<p class="goug-health-summary__description">
				<?php echo esc_html( $health['description'] ?? '' ); ?>
			</p>
		</div>
	</div>

	<?php
	\GOUG\Inc\View::render(
		'components/stat-grid',
		array(
			'items'      => $stats,
			'class_name' => 'goug-stat-grid--site-health',
		)
	);
	?>

	<?php if ( ! empty( $health['url'] ) ) : ?>
		<div class="goug-health-summary__footer">
			<a href="<?php echo esc_url( $health['url'] ); ?>">
				<?php
				esc_html_e(
					'View full Site Health report',
					'goug-framework'
				);
				?>

				<span
					class="dashicons dashicons-arrow-right-alt2"
					aria-hidden="true"
				></span>
			</a>
		</div>
	<?php endif; ?>

</div>