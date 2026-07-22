<?php
/**
 * Dashboard Storage Usage component.
 *
 * @var array $storage Storage usage data.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$storage = isset( $storage ) && is_array( $storage )
	? $storage
	: array();

$items = isset( $storage['items'] )
	&& is_array( $storage['items'] )
		? $storage['items']
		: array();

if ( empty( $items ) ) {
	return;
}

$percentages = array();

foreach ( $items as $item ) {
	$id = isset( $item['id'] )
		? sanitize_html_class( $item['id'] )
		: '';

	if ( '' === $id ) {
		continue;
	}

	$percentages[ $id ] = isset( $item['percentage'] )
		? max( 0, min( 100, (float) $item['percentage'] ) )
		: 0;
}

$uploads_end = $percentages['uploads'] ?? 0;

$database_end = $uploads_end
	+ ( $percentages['database'] ?? 0 );

$plugins_end = $database_end
	+ ( $percentages['plugins'] ?? 0 );

$themes_end = $plugins_end
	+ ( $percentages['themes'] ?? 0 );

$calculated_at = isset( $storage['calculated_at'] )
	? (int) $storage['calculated_at']
	: 0;
?>

<div class="goug-storage">

	<div class="goug-storage__visual">

		<div
			class="goug-storage-chart"
			style="<?php
			echo esc_attr(
				sprintf(
					'--uploads-end:%1$s%%;'
					. '--database-end:%2$s%%;'
					. '--plugins-end:%3$s%%;'
					. '--themes-end:%4$s%%;',
					$uploads_end,
					$database_end,
					$plugins_end,
					$themes_end
				)
			);
			?>"
		>
			<div class="goug-storage-chart__center">
				<strong>
					<?php
					echo esc_html(
						$storage['total'] ?? '0 B'
					);
					?>
				</strong>

				<span>
					<?php
					esc_html_e(
						'Total Used',
						'goug-framework'
					);
					?>
				</span>
			</div>
		</div>

	</div>

	<div class="goug-storage__details">

		<div class="goug-storage-list">

			<?php foreach ( $items as $item ) : ?>

				<?php
				if (
					! is_array( $item ) ||
					empty( $item['label'] )
				) {
					continue;
				}

				$id = isset( $item['id'] )
					? sanitize_html_class( $item['id'] )
					: 'other';
				?>

				<div class="goug-storage-item">

					<span
						class="goug-storage-item__marker goug-storage-item__marker--<?php
						echo esc_attr( $id );
						?>"
						aria-hidden="true"
					></span>

					<div class="goug-storage-item__content">
						<strong>
							<?php
							echo esc_html(
								$item['label']
							);
							?>
						</strong>

						<?php
						if ( ! empty( $item['description'] ) ) :
							?>
							<span>
								<?php
								echo esc_html(
									$item['description']
								);
								?>
							</span>
						<?php endif; ?>
					</div>

					<div class="goug-storage-item__value">
						<strong>
							<?php
							echo esc_html(
								$item['formatted'] ?? '0 B'
							);
							?>
						</strong>

						<span>
							<?php
							echo esc_html(
								number_format_i18n(
									$item['percentage'] ?? 0,
									1
								)
							);
							?>%
						</span>
					</div>

				</div>

			<?php endforeach; ?>

		</div>

		<?php if ( $calculated_at > 0 ) : ?>
			<div class="goug-storage__footer">
				<?php
				printf(
					/* translators: %s: Human-readable elapsed time. */
					esc_html__(
						'Calculated %s ago',
						'goug-framework'
					),
					esc_html(
						human_time_diff(
							$calculated_at,
							current_time(
								'timestamp',
								true
							)
						)
					)
				);
				?>
			</div>
		<?php endif; ?>

	</div>

</div>