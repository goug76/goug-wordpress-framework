<?php

namespace GOUG\Inc;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Traits\Singleton;

class Enqueue
{
    private $theme_version;
    use Singleton;

    protected function __construct() 
    {
        $theme = wp_get_theme();
        $this->theme_version = $theme->get('Version'); // Set version once

        $this->setup_hooks();
    }

    protected function setup_hooks() 
    {
        add_action('wp_enqueue_scripts', array( $this, 'enqueue_scripts' ));
        add_action('wp_head', array( $this, 'goug_head'), 5);
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ) );
    }

    public function enqueue_scripts() 
    {        
        // Registering CSS Stylesheets
        wp_register_style('main_style', get_stylesheet_directory_uri() . '/assets/css/theme_css.css', [], $this->theme_version);

        // Enqueue CSS Stylesheets
        wp_enqueue_style('dashicons');
        wp_enqueue_style('main_style');

        // Registering JS Scripts
        wp_register_script('main-js', get_stylesheet_directory_uri() . '/assets/js/theme_js.js', [], $this->theme_version, true);

        // Enqueue JS Scripts
        wp_enqueue_script('main-js');
    }

    public function goug_head() 
    { ?>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://cdnjs.cloudflare.com">
        <link rel="preconnect" href="https://kit.fontawesome.com">

        <?php
    }

    public function admin_enqueue()
    {
        // Registering CSS Stylesheets
        wp_register_style('admin_style', get_stylesheet_directory_uri() . '/assets/css/admin_css.css', [], $this->theme_version);

        // Enqueue CSS Stylesheets
        wp_enqueue_style('admin_style');

        // Registering JS Scripts
        wp_register_script('admin-js', get_stylesheet_directory_uri() . '/assets/js/admin_js.js', [], $this->theme_version, true);

        // Enqueue JS Scripts
        wp_enqueue_script('admin-js');
    }
}