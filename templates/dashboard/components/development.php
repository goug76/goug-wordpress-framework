<?php
/**
 * Dashboard Development component.
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

$environment = isset( $development['environment'] )
	&& is_array( $development['environment'] )
		? $development['environment']
		: array();

$theme = isset( $development['theme'] )
	&& is_array( $development['theme'] )
		? $development['theme']
		: array();

$runtime = isset( $development['runtime'] )
	&& is_array( $development['runtime'] )
		? $development['runtime']
		: array();

$debug = isset( $development['debug'] )
	&& is_array( $development['debug'] )
		? $development['debug']
		: array();

$actions = isset( $development['actions'] )
	&& is_array( $development['actions'] )
		? $development['actions']
		: array();

$environment_state = ! empty( $environment['is_local'] )
	? 'development'
	: 'production';
?>

<div class="goug-development">

	<div class="goug-development__summary">

		<div
			class="goug-development-environment goug-development-environment--<?php
			echo esc_attr( $environment_state );
			?>"
		>
			<span
				class="goug-development-environment__icon dashicons dashicons-admin-site-alt3"
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
						$environment['label'] ?? ''
					);
					?>
				</strong>
			</div>
		</div>

		<div class="goug-development-facts">

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'Active Theme', 'goug-framework' ); ?>
				</span>

				<strong>
					<?php echo esc_html( $theme['name'] ?? '' ); ?>
				</strong>

				<small>
					<?php
					printf(
						/* translators: %s: Theme version. */
						esc_html__( 'Version %s', 'goug-framework' ),
						esc_html( $theme['version'] ?? '' )
					);
					?>
				</small>
			</div>

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'Theme Type', 'goug-framework' ); ?>
				</span>

				<strong>
					<?php
					echo ! empty( $theme['is_child_theme'] )
						? esc_html__(
							'Child Theme',
							'goug-framework'
						)
						: esc_html__(
							'Parent Theme',
							'goug-framework'
						);
					?>
				</strong>

				<?php if ( ! empty( $theme['parent_name'] ) ) : ?>
					<small>
						<?php
						printf(
							/* translators: %s: Parent theme name. */
							esc_html__(
								'Parent: %s',
								'goug-framework'
							),
							esc_html( $theme['parent_name'] )
						);
						?>
					</small>
				<?php endif; ?>
			</div>

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'Framework', 'goug-framework' ); ?>
				</span>

				<strong>
					<?php
					echo esc_html(
						$runtime['framework_version'] ?? ''
					);
					?>
				</strong>

				<small>
					<?php
					printf(
						/* translators: %s: WordPress version. */
						esc_html__(
							'WordPress %s',
							'goug-framework'
						),
						esc_html(
							$runtime['wordpress_version'] ?? ''
						)
					);
					?>
				</small>
			</div>

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'PHP', 'goug-framework' ); ?>
				</span>

				<strong>
					<?php
					echo esc_html(
						$runtime['php_version'] ?? ''
					);
					?>
				</strong>

				<small>
					<?php
					echo ! empty( $debug['script_debug'] )
						? esc_html__(
							'Unminified assets',
							'goug-framework'
						)
						: esc_html__(
							'Production assets',
							'goug-framework'
						);
					?>
				</small>
			</div>

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'Debug Mode', 'goug-framework' ); ?>
				</span>

				<strong
					class="<?php
					echo ! empty( $debug['enabled'] )
						? 'is-enabled'
						: 'is-disabled';
					?>"
				>
					<?php
					echo ! empty( $debug['enabled'] )
						? esc_html__(
							'Enabled',
							'goug-framework'
						)
						: esc_html__(
							'Disabled',
							'goug-framework'
						);
					?>
				</strong>

				<small>
					<?php
					echo ! empty( $debug['log_enabled'] )
						? esc_html__(
							'Logging enabled',
							'goug-framework'
						)
						: esc_html__(
							'Logging disabled',
							'goug-framework'
						);
					?>
				</small>
			</div>

			<div class="goug-development-fact">
				<span>
					<?php esc_html_e( 'Debug Log', 'goug-framework' ); ?>
				</span>

				<strong>
					<?php
					echo ! empty( $debug['log_exists'] )
						? esc_html__(
							'Available',
							'goug-framework'
						)
						: esc_html__(
							'Not Found',
							'goug-framework'
						);
					?>
				</strong>

				<small>
					<?php
					echo ! empty( $debug['log_readable'] )
						? esc_html__(
							'Readable',
							'goug-framework'
						)
						: esc_html__(
							'Unavailable',
							'goug-framework'
						);
					?>
				</small>
			</div>

		</div>

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

				$protocols = ! empty( $action['protocols'] )
					&& is_array( $action['protocols'] )
						? $action['protocols']
						: array( 'http', 'https' );

				$url = esc_url(
					$action['url'],
					$protocols
				);

				if ( '' === $url ) {
					continue;
				}
				?>

				<a
					class="goug-development-action"
					href="<?php echo $url; ?>"
					<?php if ( ! empty( $action['external'] ) ) : ?>
						target="_blank"
						rel="noopener noreferrer"
					<?php endif; ?>
				>
					<?php
					$svg_icon_url = ! empty( $action['icon_svg'] )
						? \GOUG\Inc\Helpers\get_icon_url(
							$action['icon_svg']
						)
						: '';
					?>

					<span class="goug-development-action__icon" aria-hidden="true">

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
								echo esc_attr(
									sanitize_html_class(
										$action['icon']
										?? 'dashicons-admin-tools'
									)
								);
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

						<small>
							<?php
							echo esc_html(
								$action['description'] ?? ''
							);
							?>
						</small>
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