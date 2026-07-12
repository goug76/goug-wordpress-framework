<?php
defined( 'ABSPATH' ) || exit;
/**
 * GeneratePress child theme functions and definitions.
 *
 * Add your custom PHP in this file.
 * Only edit this file if you have direct access to it on your server (to fix errors if they happen).
 */

$files = array( 
    '/inc/helpers/autoloader.php',
    '/inc/helpers/helper-functions.php'
);

foreach($files as $file) {
    if ( file_exists( dirname( __FILE__ ) . $file ) ) {
        require_once dirname( __FILE__ ) . $file;
    }
}

if ( ! defined( 'GOUG_DIR_PATH' ) ) {
	define( 'GOUG_DIR_PATH', untrailingslashit( get_stylesheet_directory() ) );
}

\GOUG\Inc\FRAMEWORK::get_instance();

add_action('init', function() {
    flush_rewrite_rules();
});