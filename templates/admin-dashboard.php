<?php
/**
 * Custom Admin Dashboard Template
 *
 * @package GOUG
 */
defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$user_name = $current_user->display_name; // Full name or username

// Ensure notices appear before anything else
echo '<div class="goug-notices">';
do_action('admin_notices'); // Standard notices
do_action('all_admin_notices'); // Some plugins use this
echo '</div>';

?>

<div class="wrap goug-dashboard">
    <div class="goug-conainer goug-dashboard-header">
        <a href="<?php echo GOUG_LAB_URL; ?>" target="_blank" rel="noopener noreferrer">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/gouglabs_logo_dark.webp') ?>" alt="Gouge Labs Logo">
        </a>
        <h1>Hi <?php echo esc_html( $user_name ); ?>, Welcome to <?php echo get_bloginfo('name'); ?> Dashboard</h1>
        <p>This is your personalized admin area where you can manage everything at a glance.</p>
    </div> <!-- End goug-dashboard-header -->
    <div class="goug-section">
        <h2><?php echo get_bloginfo('name'); ?> at a Glance</h2>
        <div class="goug-section-flex">
            <div class="goug-essential-actions">
                <h3>Essential Actions</h3>
                <div class="goug-flex">
                    <a href="<?php echo admin_url('edit.php'); ?>" class="goug-actions" title="View and manage posts">
                        <span class="dashicons dashicons-admin-post"></span>
                        <h4>Posts</h4>
                    </a>
                    <a href="<?php echo admin_url('edit.php?post_type=page'); ?>" class="goug-actions" title="View and manage pages">
                        <span class="dashicons dashicons-admin-page"></span>
                        <h4>Pages</h4>
                    </a>

                    <!-- Check if Course Post Type Exists -->
                    <?php if (post_type_exists('courses')) : ?>
                        <a href="<?php echo admin_url('admin.php?page=tutor'); ?>" class="goug-actions">
                            <span class="dashicons dashicons-welcome-learn-more"></span>
                            <h4>Courses</h4>
                        </a>
                    <?php endif; ?>

                    <a href="<?php echo admin_url('plugins.php'); ?>" class="goug-actions" title="View and manage plugins">
                        <span class="dashicons dashicons-admin-plugins"></span>
                        <h4>Plugins</h4>
                    </a>
                    <a href="<?php echo admin_url('themes.php'); ?>" class="goug-actions" title="View and manage theme">
                        <span class="dashicons dashicons-admin-appearance"></span>
                        <h4>Theme</h4>
                    </a>
                    <a href="<?php echo home_url(); ?>" class="goug-actions" target="_blank" rel="noopener noreferrer" title="Visit Site">
                        <span class="dashicons dashicons-admin-site"></span>
                        <h4>Site</h4>
                    </a>
                </div> <!-- End goug-flex -->
            </div> <!-- End goug-essential-actions -->
            <div class="goug-site-actions">
                <h3>Site Settings Actions</h3>
                <div class="goug-flex">
                    <a href="<?php echo admin_url('update-core.php'); ?>" class="goug-actions" title="Security & Updates">
                        <span class="dashicons dashicons-update"></span>
                        <h4>Updates</h4>
                    </a>
                    <a href="<?php echo admin_url('users.php'); ?>" class="goug-actions" title="Manage Users">
                        <span class="dashicons dashicons-admin-users"></span>
                        <h4>Users</h4>
                    </a>
                    <a href="<?php echo admin_url('options-permalink.php'); ?>" class="goug-actions" title="Manage Permalinks">
                        <span class="dashicons dashicons-admin-links"></span>
                        <h4>Permalinks</h4>
                    </a>
                    <?php if (current_user_can('manage_options')) : ?>
                        <a href="<?php echo admin_url('site-health.php'); ?>" class="goug-actions" title="Site Health">
                            <span class="dashicons dashicons-heart"></span>
                            <h4>Health</h4>
                        </a>
                    <?php endif; ?>
                </div>
            </div><!-- End goug-site-actions -->
        </div> <!-- End goug-section-flex -->
        <div class="goug-section-flex">
            <div class="goug-site-actions">
                    <h3>Site Design Actions</h3>
                    <div class="goug-flex">
                        <!-- Menus -->
                        <a href="<?php echo admin_url('nav-menus.php'); ?>" class="goug-actions" title="Site Menus">
                            <span class="dashicons dashicons-menu"></span>
                            <h4>Menus</h4>
                        </a>

                        <!-- Widgets -->
                        <a href="<?php echo admin_url('widgets.php'); ?>" class="goug-actions" title="Site Widgets">
                            <span class="dashicons dashicons-admin-generic"></span>
                            <h4>Widgets</h4>
                        </a>

                        <!-- Theme Customizer -->
                        <a href="<?php echo admin_url('customize.php'); ?>" class="goug-actions" title="Site Customizer">
                            <span class="dashicons dashicons-admin-customizer"></span>
                            <h4>Customize</h4>
                        </a>

                        <!-- Theme File Editor -->
                        <a href="<?php echo admin_url('theme-editor.php'); ?>" class="goug-actions" title="Theme Editor">
                            <span class="dashicons dashicons-editor-code"></span>
                            <h4>Editor</h4>
                        </a>
                    </div>
                </div><!-- End goug-site-actions -->
            <div class="goug-site-overview">
                <h3>Site Overview</h3>
                <div class="goug-flex">
                    <div class="goug-actions goug-stats">
                        <span class="dashicons dashicons-admin-post"></span>
                        <h4><?php echo wp_count_posts()->publish; ?> Posts</h4>
                    </div>
                    <div class="goug-actions goug-stats">
                        <span class="dashicons dashicons-admin-page"></span>
                        <h4><?php echo wp_count_posts('page')->publish; ?> Pages</h4>
                    </div>
                    <div class="goug-actions goug-stats">
                        <span class="dashicons dashicons-admin-comments"></span>
                        <h4><?php echo wp_count_comments()->approved; ?> Comments</h4>
                    </div>
                    <div class="goug-actions goug-stats">
                        <span class="dashicons dashicons-admin-users"></span>
                        <h4><?php echo count_users()['total_users']; ?> Users</h4>
                    </div>
                </div> <!-- End goug-flex -->
            </div> <!-- End goug-site-overview -->
        </div> <!-- End goug-section-flex -->
        <div class="goug updates">
            <h3>System Updates</h3>
            <div class="goug-flex">
                <!-- WordPress Core Updates -->
                <div class="goug-update-item">
                    <span class="dashicons dashicons-wordpress"></span>
                    <p>WordPress Core</p>
                    <?php
                    $core_updates = get_core_updates();
                    if (!empty($core_updates) && !empty($core_updates[0]->response) && $core_updates[0]->response === 'upgrade') {
                        echo '<span class="status warning">Update Available</span>';
                    } else {
                        echo '<span class="status success">Up-to-date</span>';
                    }
                    ?>
                </div>

                <!-- Plugin Updates -->
                <div class="goug-update-item">
                    <span class="dashicons dashicons-admin-plugins"></span>
                    <p>Plugins</p>
                    <?php
                    $plugin_updates = wp_get_update_data()['counts']['plugins'];
                    if ($plugin_updates > 0) {
                        echo '<span class="status warning">' . esc_html($plugin_updates) . ' Update(s) Available</span>';
                    } else {
                        echo '<span class="status success">Up-to-date</span>';
                    }
                    ?>
                </div>

                <!-- Theme Updates -->
                <div class="goug-update-item">
                    <span class="dashicons dashicons-admin-appearance"></span>
                    <p>Themes</p>
                    <?php
                    $theme_updates = wp_get_update_data()['counts']['themes'];
                    if ($theme_updates > 0) {
                        echo '<span class="status warning">' . esc_html($theme_updates) . ' Update(s) Available</span>';
                    } else {
                        echo '<span class="status success">Up-to-date</span>';
                    }
                    ?>
                </div>

                <!-- Translation Updates -->
                <div class="goug-update-item">
                    <span class="dashicons dashicons-translation"></span>
                    <p>Translations</p>
                    <?php
                    $translation_updates = wp_get_update_data()['counts']['translations'];
                    if ($translation_updates > 0) {
                        echo '<span class="status warning">' . esc_html($translation_updates) . ' Update(s) Available</span>';
                    } else {
                        echo '<span class="status success">Up-to-date</span>';
                    }
                    ?>
                </div>
            </div>
        </div> <!-- End goug updates -->
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
