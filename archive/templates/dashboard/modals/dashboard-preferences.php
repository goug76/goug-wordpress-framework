<?php
/**
 * Dashboard Preferences modal.
 *
 * @var string $id               Modal element ID.
 * @var string $title            Modal title.
 * @var string $density          Current density.
 * @var array  $density_options  Available density options.
 * @var bool   $show_greeting    Whether the greeting is enabled.
 * @var bool   $enable_motion    Whether animations are enabled.
 * @var int    $hidden_panels    Number of hidden panels.
 * @var array  $available_panels Available dashboard panel definitions.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$modal_id = isset( $id )
	? sanitize_html_class( $id )
	: 'goug-dashboard-preferences-modal';

$modal_title = isset( $title )
	? (string) $title
	: __( 'Configure Dashboard', 'goug-framework' );

$current_density = isset( $density )
	? sanitize_key( $density )
	: 'comfortable';

$options = isset( $density_options ) && is_array( $density_options )
	? $density_options
	: array();

$greeting_enabled = ! empty( $show_greeting );
$motion_enabled   = ! empty( $enable_motion );

$hidden_count = isset( $hidden_panels )
	? absint( $hidden_panels )
	: 0;

$panels = isset( $available_panels )
	&& is_array( $available_panels )
		? $available_panels
		: array();
?>

<div
	id="<?php echo esc_attr( $modal_id ); ?>"
	style="display: none;"
>
	<form
        class="goug-dashboard-preferences-modal"
        method="post"
        action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"
    >
        <input
            type="hidden"
            name="action"
            value="goug_save_dashboard_preferences"
        >

        <?php
        wp_nonce_field(
            'goug_save_dashboard_preferences',
            'goug_dashboard_preferences_nonce'
        );
        ?>

		<header class="goug-dashboard-preferences-modal__header">

			<h2 class="goug-dashboard-preferences-modal__title">
				<?php echo esc_html( $modal_title ); ?>
			</h2>

			<p class="description">
				<?php
				echo esc_html__(
					'Customize how your dashboard looks and behaves.',
					'goug-framework'
				);
				?>
			</p>

		</header>

		<section
			class="goug-dashboard-preferences-modal__section"
			aria-labelledby="goug-dashboard-appearance-heading"
		>

			<h3 id="goug-dashboard-appearance-heading">
				<?php
				echo esc_html__(
					'Appearance',
					'goug-framework'
				);
				?>
			</h3>

			<table class="form-table" role="presentation">
				<tbody>

					<tr>
						<th scope="row">
							<label for="goug-dashboard-density">
								<?php
									echo esc_html__(
										'Dashboard Density',
										'goug-framework'
									);
								?>
							</label>
						</th>

						<td>
							<select
								id="goug-dashboard-density"
								name="density"
							>
								<?php foreach ( $options as $value => $label ) : ?>

									<option
										value="<?php echo esc_attr( $value ); ?>"
										<?php
										selected(
											$current_density,
											$value
										);
										?>
									>
										<?php echo esc_html( $label ); ?>
									</option>

								<?php endforeach; ?>
							</select>

							<p class="description">
								<?php
									echo esc_html__(
										'Controls spacing within dashboard panels.',
										'goug-framework'
									);
								?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php
								echo esc_html__(
									'Greeting',
									'goug-framework'
								);
							?>
						</th>

						<td>
							<label for="goug-dashboard-show-greeting">

								<input
									type="checkbox"
									id="goug-dashboard-show-greeting"
									name="show_greeting"
									value="1"
									<?php checked( $greeting_enabled ); ?>
								>

								<?php
									echo esc_html__(
										'Show the dashboard greeting',
										'goug-framework'
									);
								?>

							</label>

							<p class="description">
								<?php
									echo esc_html__(
										'Displays a personalized greeting at the top of the dashboard.',
										'goug-framework'
									);
								?>
							</p>
						</td>
					</tr>

					<tr>
						<th scope="row">
							<?php
								echo esc_html__(
									'Animations',
									'goug-framework'
								);
							?>
						</th>

						<td>
							<label for="goug-dashboard-enable-motion">

								<input
									type="checkbox"
									id="goug-dashboard-enable-motion"
									name="enable_motion"
									value="1"
									<?php checked( $motion_enabled ); ?>
								>

								<?php
									echo esc_html__(
										'Enable dashboard animations',
										'goug-framework'
									);
								?>

							</label>

							<p class="description">
								<?php
									echo esc_html__(
										'Controls decorative transitions and motion effects.',
										'goug-framework'
									);
								?>
							</p>
						</td>
					</tr>

				</tbody>
			</table>

		</section>

		<section
			class="goug-dashboard-preferences-modal__section"
			aria-labelledby="goug-dashboard-panels-heading"
		>

			<h3 id="goug-dashboard-panels-heading">
				<?php
					echo esc_html__(
						'Panels',
						'goug-framework'
					);
				?>
			</h3>

			<table class="form-table" role="presentation">
				<tbody>

					<tr>
                        <th scope="row">
                            <?php
                            echo esc_html__(
                                'Visible Panels',
                                'goug-framework'
                            );
                            ?>
                        </th>

                        <td>
                            <?php if ( ! empty( $panels ) ) : ?>

                                <fieldset class="goug-dashboard-panel-visibility">

                                    <legend class="screen-reader-text">
                                        <?php
                                        echo esc_html__(
                                            'Choose which panels appear on your dashboard',
                                            'goug-framework'
                                        );
                                        ?>
                                    </legend>

                                    <?php foreach ( $panels as $panel ) : ?>

                                        <?php
                                        $panel_id = isset( $panel['id'] )
                                            ? sanitize_key( $panel['id'] )
                                            : '';

                                        if ( '' === $panel_id ) {
                                            continue;
                                        }

                                        $panel_title = isset( $panel['title'] )
                                            ? (string) $panel['title']
                                            : $panel_id;

                                        $is_visible = ! empty(
                                            $panel['visible']
                                        );

                                        $input_id = sprintf(
                                            'goug-dashboard-panel-%s',
                                            $panel_id
                                        );
                                        ?>

                                        <input
                                            type="hidden"
                                            name="available_panel_ids[]"
                                            value="<?php echo esc_attr( $panel_id ); ?>"
                                        >

                                        <label
                                            for="<?php echo esc_attr( $input_id ); ?>"
                                            class="goug-dashboard-panel-visibility__option"
                                        >
                                            <input
                                                type="checkbox"
                                                id="<?php echo esc_attr( $input_id ); ?>"
                                                name="visible_panels[]"
                                                value="<?php echo esc_attr( $panel_id ); ?>"
                                                <?php checked( $is_visible ); ?>
                                            >

                                            <span>
                                                <?php echo esc_html( $panel_title ); ?>
                                            </span>
                                        </label>

                                    <?php endforeach; ?>

                                </fieldset>

                                <p class="description">
                                    <?php
                                    echo esc_html__(
                                        'Clear a panel to remove it from your dashboard. You can restore it here at any time.',
                                        'goug-framework'
                                    );
                                    ?>
                                </p>

                            <?php else : ?>

                                <p class="description">
                                    <?php
                                    echo esc_html__(
                                        'No configurable dashboard panels are available.',
                                        'goug-framework'
                                    );
                                    ?>
                                </p>

                            <?php endif; ?>
                        </td>
                    </tr>

				</tbody>
			</table>

		</section>

		<footer class="goug-dashboard-preferences-modal__footer">

			<p class="submit">

				<button
                    type="submit"
                    class="button button-primary"
                >
					<?php
						echo esc_html__(
							'Save Preferences',
							'goug-framework'
						);
					?>
				</button>

			</p>

			<p class="description">
                <?php
                echo esc_html__(
                    'Preferences are saved to your user profile.',
                    'goug-framework'
                );
                ?>
            </p>

		</footer>

    </form>
</div>