<?php
/**
 * Dashboard Recent Activity component.
 *
 * @var array $items Activity items.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$items = isset( $items ) && is_array( $items )
	? $items
	: array();

if ( empty( $items ) ) :
	?>

	<div class="goug-activity-empty">
		<span
			class="goug-activity-empty__icon dashicons dashicons-clock"
			aria-hidden="true"
		></span>

		<p>
			<?php
			esc_html_e(
				'No recent activity was found.',
				'goug-framework'
			);
			?>
		</p>
	</div>

	<?php
	return;
endif;
?>

<div class="goug-activity-feed">

	<?php foreach ( $items as $item ) : ?>

		<?php
		if (
			! is_array( $item ) ||
			empty( $item['title'] )
		) {
			continue;
		}

		$state = isset( $item['state'] )
			? sanitize_html_class( $item['state'] )
			: 'neutral';

		$icon = isset( $item['icon'] )
			? sanitize_html_class( $item['icon'] )
			: 'dashicons-marker';

		$action = isset( $item['action'] )
			? (string) $item['action']
			: '';

		$title = (string) $item['title'];

		$url = isset( $item['url'] )
			? (string) $item['url']
			: '';

		$timestamp = isset( $item['timestamp'] )
			? (int) $item['timestamp']
			: 0;

		$time_text = $timestamp > 0
			? sprintf(
				/* translators: %s: Human-readable time difference. */
				__( '%s ago', 'goug-framework' ),
				human_time_diff(
					$timestamp,
					current_time( 'timestamp', true )
				)
			)
			: '';
		?>

		<div class="goug-activity-item">

			<span
				class="goug-activity-item__marker goug-activity-item__marker--<?php
				echo esc_attr( $state );
				?>"
			>
				<span
					class="dashicons <?php echo esc_attr( $icon ); ?>"
					aria-hidden="true"
				></span>
			</span>

			<div class="goug-activity-item__content">

				<div class="goug-activity-item__text">

                    <?php if ( '' !== $url ) : ?>
                        <a
                            class="goug-activity-item__title"
                            href="<?php echo esc_url( $url ); ?>"
                        >
                            <?php echo esc_html( $title ); ?>
                        </a>
                    <?php else : ?>
                        <span class="goug-activity-item__title">
                            <?php echo esc_html( $title ); ?>
                        </span>
                    <?php endif; ?>

                    <?php if ( '' !== $action ) : ?>
                        <span class="goug-activity-item__action">
                            <?php echo esc_html( $action ); ?>
                        </span>
                    <?php endif; ?>

                </div>

				<?php if ( '' !== $time_text ) : ?>
					<time
						class="goug-activity-item__time"
						datetime="<?php
						echo esc_attr(
							gmdate( 'c', $timestamp )
						);
						?>"
					>
						<?php echo esc_html( $time_text ); ?>
					</time>
				<?php endif; ?>

			</div>

		</div>

	<?php endforeach; ?>

</div>