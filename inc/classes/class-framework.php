<?php

namespace GOUG\Inc;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Traits\Singleton;

class FRAMEWORK 
{
    use Singleton;

    protected function __construct() 
    { 
        /**
		 * Load Classes
		 */
        
        Enqueue::get_instance();
        Dashboard::get_instance();
        //Admin_Page_Redirect::get_instance();
 
        $this->setup_hooks();
    }

    protected function setup_hooks() 
    {
        /**
		 * Actions
		 */

        // add_action( 'init', array( $this, 'restrict_admin_dashboard') );
        // add_filter( 'login_redirect', array( $this, 'goug_logon_redirect' ) );
        add_filter( 'get_avatar', array( $this, 'bloggerpilot_gravatar_alt' ) );

    }

    public function restrict_admin_dashboard() 
    {
        if ( is_admin() && ! current_user_can( 'administrator' ) ) {
            wp_redirect( home_url() );
            exit;
        }
    }

    public function goug_logon_redirect( $requested_redirect_to ) 
    {
        return $requested_redirect_to;
    }

    // Add alt tag to WordPress Gravatar images
    function bloggerpilot_gravatar_alt($bloggerpilotGravatar) 
    {
        if (have_comments()) {
            $alt = get_comment_author();
        }
        else {
            $alt = get_the_author_meta('display_name');
        }
        $bloggerpilotGravatar = str_replace('alt=\'\'', 'alt=\'Avatar for ' . $alt . '\'', $bloggerpilotGravatar);
        return $bloggerpilotGravatar;
    }
}