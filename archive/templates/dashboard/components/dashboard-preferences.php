<?php
/**
 * Dashboard Preferences summary component.
 *
 * @var array $density       Density preference data.
 * @var array $greeting      Greeting preference data.
 * @var array $animations    Animation preference data.
 * @var array $hidden_panels Hidden-panel preference data.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$items = array(
	isset( $density ) && is_array( $density )
		? $density
		: array(),

	isset( $greeting ) && is_array( $greeting )
		? $greeting
		: array(),

	isset( $animations ) && is_array( $animations )
		? $animations
		: array(),

	isset( $hidden_panels ) && is_array( $hidden_panels )
		? $hidden_panels
		: array(),
);

$items = array_filter(
	$items,
	static function ( $item ) {
		return is_array( $item )
			&& ! empty( $item['label'] );
	}
);
?>

<div class="goug-preferences-summary">

	<div class="goug-preferences-summary__items">

		<?php foreach ( $items as $item ) : ?>

			<?php
			$label = isset( $item['label'] )
				? (string) $item['label']
				: '';

			$value = isset( $item['value'] )
				? (string) $item['value']
				: '';

			$icon = isset( $item['icon'] )
				? sanitize_html_class( $item['icon'] )
				: '';

			$has_state = array_key_exists(
				'enabled',
				$item
			);

			$state_class = '';

			if ( $has_state ) {
				$state_class = ! empty( $item['enabled'] )
					? ' is-enabled'
					: ' is-disabled';
			}
			?>

			<div
				class="goug-preferences-summary__item<?php
				echo esc_attr( $state_class );
				?>"
			>
				<div class="goug-preferences-summary__label">

					<?php if ( '' !== $icon ) : ?>
						<span
							class="goug-preferences-summary__icon dashicons <?php
							echo esc_attr( $icon );
							?>"
							aria-hidden="true"
						></span>
					<?php endif; ?>

					<span>
						<?php echo esc_html( $label ); ?>
					</span>

				</div>

				<strong class="goug-preferences-summary__value">
					<?php echo esc_html( $value ); ?>
				</strong>
			</div>

		<?php endforeach; ?>

	</div>

	<?php
	if ( isset( $modal ) && is_array( $modal ) ) {
		\GOUG\Inc\View::render(
			'dashboard/modals/dashboard-preferences',
			$modal
		);
	}
	?>

	<div class="goug-preferences-summary__footer">

		<?php
		$modal_id = isset( $modal['id'] )
			? sanitize_html_class( $modal['id'] )
			: 'goug-dashboard-preferences-modal';
		?>

		<a
			href="<?php
			echo esc_url(
				sprintf(
					'#TB_inline?width=640&height=520&inlineId=%s',
					$modal_id
				)
			);
			?>"
			class="button thickbox goug-preferences-summary__configure"
			title="<?php
			echo esc_attr__(
				'Configure Dashboard',
				'goug-framework'
			);
			?>"
		>
			<span
				class="dashicons dashicons-admin-settings"
				aria-hidden="true"
			></span>

			<?php
			echo esc_html__(
				'Configure Dashboard',
				'goug-framework'
			);
			?>
		</a>

		<p class="goug-preferences-summary__note">
			<?php
			echo esc_html__(
				'Preferences are saved to your user profile.',
				'goug-framework'
			);
			?>
		</p>

	</div>

</div>