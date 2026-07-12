<?php
/**
 * Custom Admin Dashboard Template
 *
 * @package GOUG
 */
defined( 'ABSPATH' ) || exit;

// Ensure notices appear before anything else
echo '<div class="goug-notices">';
do_action('admin_notices'); // Standard notices
do_action('all_admin_notices'); // Some plugins use this
echo '</div>';

?>

<div class="wrap goug-dashboard">
    <?php
    \GOUG\Inc\View::render(
        'dashboard/components/header',
        array(
            'user_name' => $data['user']['display_name'],
            'site_name' => $data['site']['name'],
        )
    );
    ?> <!-- End goug-dashboard-header -->
    <div class="goug-section">

        <h2>
            <?php
            printf(
                /* translators: %s: Site name. */
                esc_html__( '%s at a Glance', 'goug-framework' ),
                esc_html( $data['site']['name'] )
            );
            ?>
        </h2>

        <div class="goug-section-flex">
            
            <?php
            if ( ! empty( $data['actions']['essential'] ) ) {
                \GOUG\Inc\View::render(
                    'dashboard/components/action-group',
                    array(
                        'group'      => $data['actions']['essential'],
                        'class_name' => 'goug-essential-actions',
                    )
                );
            }
            ?> 

            <?php
            if ( ! empty( $data['actions']['settings'] ) ) {
                \GOUG\Inc\View::render(
                    'dashboard/components/action-group',
                    array(
                        'group'      => $data['actions']['settings'],
                        'class_name' => 'goug-site-actions',
                    )
                );
            }
            ?>

        </div> <!-- End goug-section-flex -->

        <div class="goug-section-flex">

            <?php
            if ( ! empty( $data['actions']['design'] ) ) {
                \GOUG\Inc\View::render(
                    'dashboard/components/action-group',
                    array(
                        'group'      => $data['actions']['design'],
                        'class_name' => 'goug-site-actions goug-design-actions',
                    )
                );
            }
            ?>

            <?php
            if ( ! empty( $data['overview'] ) ) {
                \GOUG\Inc\View::render(
                    'dashboard/components/site-overview',
                    array(
                        'overview' => $data['overview'],
                    )
                );
            }
            ?>

        </div> <!-- End goug-section-flex -->
        <?php
        if ( ! empty( $data['system_updates'] ) ) {
            \GOUG\Inc\View::render(
                'dashboard/components/system-updates',
                array(
                    'system_updates' => $data['system_updates'],
                )
            );
        }
        ?>
    </div> <!-- End goug-section -->

    <?php if (is_active_sidebar('admin_dashboard_widgets')) : ?>
        <div class="goug-section">
            <h3>Custom Dashboard Widgets</h3>
            <div id="admin-dashboard-widgets">
                <?php dynamic_sidebar('admin_dashboard_widgets'); ?>
            </div>
        </div>
    <?php endif; ?> 

</div> <!-- End goug-dashboard -->
