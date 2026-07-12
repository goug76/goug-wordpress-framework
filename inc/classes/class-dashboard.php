<?php

namespace GOUG\Inc;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Traits\Singleton;

class Dashboard
{
    use Singleton;

    protected function __construct() 
    { 
        /**
		 * Load Globals
		 */
        
        if ( ! defined( 'GOUG_LAB_URL' ) ) {
            define( 'GOUG_LAB_URL', 'https://gouglabs.com' );
        }

        /**
		 * Load Classes
		 */
 
        $this->setup_hooks();
    }

    protected function setup_hooks()
    {
        /**
		 * Actions
		 */

        add_action('admin_notices', array( $this, 'remove_admin_duplicates' ), 1 );
        add_action('admin_menu', array( $this, 'replace_dashboard_with_template' ) );
        add_action('load-index.php', array( $this, 'redirect_dashboard' ) );
        add_filter('admin_body_class', array( $this, 'add_dashboard_body_class' ) );
        add_filter('admin_footer_text', array( $this, 'custom_admin_footer' ));
        add_action('wp_dashboard_setup', array( $this, 'add_dashboard_widget_area' ) );
        add_action('admin_bar_menu', array( $this, 'add_custom_logo' ) );
        add_action('admin_bar_menu', array( $this, 'remove_wp_logo' ), 999 );
        add_action('widgets_init', array( $this, 'register_admin_widget_area' ));        
    }

    public function remove_admin_duplicates()
    {
        if (get_current_screen()->id === 'toplevel_page_my-dashboard') {
            remove_all_actions('admin_notices'); // Removes duplicate calls
            do_action('admin_notices'); // Adds back a single instance
        }
    }

    public function replace_dashboard_with_template() 
    {
        // Remove the default Dashboard from the menu
        remove_menu_page('index.php');
    
        // Add a new Dashboard menu item that loads our custom page
        add_menu_page(
            'My Dashboard',
            'Dashboard',
            'manage_options',
            'my-dashboard',
            array( $this, 'load_custom_dashboard_template' ),
            'dashicons-dashboard', // Dashboard Icon
            2 // Menu Position
        );
    }
    
    public function redirect_dashboard() 
    {
        // Redirect default dashboard to the custom one
        if (is_admin() && get_current_screen()->base === 'dashboard') {
            wp_redirect(admin_url('admin.php?page=my-dashboard'));
            exit;
        }
    }
    
    public function load_custom_dashboard_template() 
    {
        require_once get_stylesheet_directory() . '/templates/admin-dashboard.php';
    }

    public function add_dashboard_body_class($classes) 
    {
        $screen = get_current_screen();
        if ($screen->id === 'toplevel_page_my-dashboard') {
            $classes .= ' goug-dashboard-page ';
        }
        return $classes;
    }

    public function custom_admin_footer() 
    {
        global $wp_version;
        $theme = wp_get_theme();
        $text = "<span id='goug-admin-footer'>Powered By <strong>Goug Labs</strong> | ";
        $text = $text . "WordPress: " . $wp_version . " | ";
        $text = $text . "Theme: " . $theme->get('Name') . " | ";
        $text = $text . "Made with ❤️</span>";
        echo $text;
    }

    public function add_dashboard_widget_area()
    {
        // Clear all dashboard widgets
        global $wp_meta_boxes;
        $wp_meta_boxes['dashboard'] = array(); 
    }

    public function remove_wp_logo ($wp_admin_bar)
    {
        // Remove the WordPress Logo
        $wp_admin_bar->remove_node('wp-logo');
    }

    public function add_custom_logo($wp_admin_bar) 
    {
        // Define the custom icon URL
        $custom_icon_url = get_stylesheet_directory_uri() . '/assets/images/gouglabs_logo_dark.webp';

        // Add Custom Logo
        $wp_admin_bar->add_node([
            'id'    => 'custom-logo',
            'title' => '<img src="' . esc_url($custom_icon_url) . '" style="height: 20px; width: auto; vertical-align: middle;" />',
            'href'  => admin_url(), // Link to dashboard or any page you want
            'meta'  => ['title' => 'Go to Dashboard'],
        ]);
    }
    
    public function register_admin_widget_area() 
    {
        register_sidebar([
            'name'          => 'Admin Dashboard Widgets',
            'id'            => 'admin_dashboard_widgets',
            'before_widget' => '<div class="admin-widget">',
            'after_widget'  => '</div>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ]);
    }    
}