<?php
/**
 * Dashboard Development component.
 *
 * Responsibilities:
 *
 * - Render the environment summary.
 * - Render normalized development facts.
 * - Render capability-aware development actions.
 *
 * The component consumes prepared data only. It does not query
 * WordPress or calculate development state.
 *
 * @var array $development Development information.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$development = isset( $development )
	&& is_array( $development )
		? $development
		: array();

if ( empty( $development ) ) {
	return;
}

$summary = isset( $development['summary'] )
	&& is_array( $development['summary'] )
		? $development['summary']
		: array();

$facts = isset( $development['facts'] )
	&& is_array( $development['facts'] )
		? $development['facts']
		: array();

$actions = isset( $development['actions'] )
	&& is_array( $development['actions'] )
		? $development['actions']
		: array();

$summary_state = isset( $summary['state'] )
	? sanitize_html_class( $summary['state'] )
	: 'production';

$summary_icon = isset( $summary['icon'] )
	? sanitize_html_class( $summary['icon'] )
	: 'dashicons-admin-site-alt3';
?>

<div class="goug-development">

	<div class="goug-development__summary">

		<div
			class="goug-development-environment goug-development-environment--<?php
			echo esc_attr( $summary_state );
			?>"
		>
			<span
				class="goug-development-environment__icon dashicons <?php
				echo esc_attr( $summary_icon );
				?>"
				aria-hidden="true"
			></span>

			<div>
				<span class="goug-development-environment__eyebrow">
					<?php
					esc_html_e(
						'Environment',
						'goug-framework'
					);
					?>
				</span>

				<strong>
					<?php
					echo esc_html(
						$summary['label'] ?? ''
					);
					?>
				</strong>
			</div>
		</div>

		<?php if ( ! empty( $facts ) ) : ?>

			<div class="goug-development-facts">

				<?php foreach ( $facts as $fact ) : ?>

					<?php
					if (
						! is_array( $fact )
						|| empty( $fact['label'] )
					) {
						continue;
					}

					$fact_state = isset( $fact['state'] )
						? sanitize_html_class(
							$fact['state']
						)
						: 'default';
					?>

					<div
						class="goug-development-fact goug-development-fact--<?php
						echo esc_attr( $fact_state );
						?>"
					>
						<span>
							<?php
							echo esc_html(
								$fact['label']
							);
							?>
						</span>

						<strong>
							<?php
							echo esc_html(
								$fact['value'] ?? ''
							);
							?>
						</strong>

						<?php
						if (
							! empty( $fact['description'] )
						) :
							?>
							<small>
								<?php
								echo esc_html(
									$fact['description']
								);
								?>
							</small>
						<?php endif; ?>
					</div>

				<?php endforeach; ?>

			</div>

		<?php endif; ?>

	</div>

	<?php if ( ! empty( $actions ) ) : ?>

		<div class="goug-development-actions">

			<?php foreach ( $actions as $action ) : ?>

				<?php
				if (
					! is_array( $action )
					|| empty( $action['label'] )
					|| empty( $action['url'] )
				) {
					continue;
				}

				$protocols = ! empty(
					$action['protocols']
				) && is_array(
					$action['protocols']
				)
					? $action['protocols']
					: array( 'http', 'https' );

				$url = esc_url(
					$action['url'],
					$protocols
				);

				if ( '' === $url ) {
					continue;
				}

				$svg_icon_url = ! empty(
					$action['icon_svg']
				)
					? \GOUG\Inc\Helpers\get_icon_url(
						$action['icon_svg']
					)
					: '';

				$dashicon = isset( $action['icon'] )
					? sanitize_html_class(
						$action['icon']
					)
					: 'dashicons-admin-tools';
				?>

				<a
					class="goug-development-action"
					href="<?php echo $url; ?>"
					<?php if ( ! empty( $action['external'] ) ) : ?>
						target="_blank"
						rel="noopener noreferrer"
					<?php endif; ?>
				>
					<span
						class="goug-development-action__icon"
						aria-hidden="true"
					>
						<?php if ( '' !== $svg_icon_url ) : ?>

							<span
								class="goug-svg-icon"
								style="<?php
								echo esc_attr(
									'--goug-icon-url: url('
									. $svg_icon_url
									. ');'
								);
								?>"
							></span>

						<?php else : ?>

							<span
								class="dashicons <?php
								echo esc_attr( $dashicon );
								?>"
							></span>

						<?php endif; ?>
					</span>

					<span class="goug-development-action__content">
						<strong>
							<?php
							echo esc_html(
								$action['label']
							);
							?>
						</strong>

						<?php
						if (
							! empty( $action['description'] )
						) :
							?>
							<small>
								<?php
								echo esc_html(
									$action['description']
								);
								?>
							</small>
						<?php endif; ?>
					</span>

					<span
						class="goug-development-action__arrow dashicons dashicons-arrow-right-alt2"
						aria-hidden="true"
					></span>
				</a>

			<?php endforeach; ?>

		</div>

	<?php endif; ?>

</div>