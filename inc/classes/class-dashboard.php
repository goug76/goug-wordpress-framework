<?php
/**
 * Custom WordPress admin dashboard coordinator.
 *
 * Handles dashboard registration, redirection, branding,
 * template rendering, and the custom dashboard widget area.
 *
 * @package GOUG
 */

namespace GOUG\Inc;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Traits\Singleton;
use GOUG\Inc\Dashboard\Dashboard_Data;
use function GOUG\Inc\Helpers\get_brand_logo_url;
use WP_Admin_Bar;

class Dashboard {

	use Singleton;

	/**
	 * Custom dashboard page slug.
	 *
	 * @var string
	 */
	private const PAGE_SLUG = 'goug-dashboard';

	/**
	 * Capability required to access the custom dashboard.
	 *
	 * We are keeping this restricted to administrators for now because
	 * the current dashboard contains links to themes, plugins, users,
	 * updates, and other administrative screens.
	 *
	 * @var string
	 */
	private const CAPABILITY = 'manage_options';

	/**
	 * Dashboard page hook returned by add_menu_page().
	 *
	 * This will become useful later when loading dashboard-only assets.
	 *
	 * @var string
	 */
	private $page_hook = '';

	/**
	 * Initialize the dashboard module.
	 */
	protected function __construct() {

		/*
		 * Keep the existing Goug Labs brand URL available to the
		 * current dashboard template.
		 */
		if ( ! defined( 'GOUG_LAB_URL' ) ) {
			define( 'GOUG_LAB_URL', 'https://gouglabs.com' );
		}

		$this->setup_hooks();
	}

	/**
	 * Register WordPress actions and filters.
	 *
	 * @return void
	 */
	protected function setup_hooks() {

		/*
		 * Dashboard registration and behavior.
		 */
		add_action(
			'admin_menu',
			array( $this, 'register_dashboard_page' )
		);

		add_action(
			'load-index.php',
			array( $this, 'redirect_default_dashboard' )
		);

		add_filter(
			'admin_body_class',
			array( $this, 'add_dashboard_body_class' )
		);

		/*
		 * Existing notice handling.
		 *
		 * We are leaving this behavior in place until the dashboard
		 * template is refactored. The current template manually renders
		 * admin notices, so changing both pieces at once would make
		 * troubleshooting harder.
		 */
		add_action(
			'admin_notices',
			array( $this, 'remove_admin_duplicates' ),
			1
		);

		/*
		 * Global WordPress admin branding.
		 */
		add_filter(
			'admin_footer_text',
			array( $this, 'custom_admin_footer' )
		);

		add_action(
			'admin_bar_menu',
			array( $this, 'add_custom_logo' )
		);

		add_action(
			'admin_bar_menu',
			array( $this, 'remove_wp_logo' ),
			999
		);

		/*
		 * Optional custom dashboard widget area.
		 */
		add_action(
			'widgets_init',
			array( $this, 'register_admin_widget_area' )
		);
	}

	/**
	 * Register the replacement dashboard page.
	 *
	 * The default dashboard menu is removed only for users who can
	 * access the custom dashboard. Other users retain the standard
	 * WordPress dashboard.
	 *
	 * @return void
	 */
	public function register_dashboard_page() {

		if ( ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		remove_menu_page( 'index.php' );

		$this->page_hook = add_menu_page(
			__( 'Dashboard', 'goug-framework' ),
			__( 'Dashboard', 'goug-framework' ),
			self::CAPABILITY,
			self::PAGE_SLUG,
			array( $this, 'render_dashboard' ),
			'dashicons-dashboard',
			2
		);
	}

	/**
	 * Redirect the native WordPress dashboard to the custom dashboard.
	 *
	 * Users who cannot access the custom dashboard remain on the
	 * standard WordPress dashboard.
	 *
	 * @return void
	 */
	public function redirect_default_dashboard() {

		if ( ! is_admin() || ! current_user_can( self::CAPABILITY ) ) {
			return;
		}

		wp_safe_redirect(
			admin_url(
				sprintf(
					'admin.php?page=%s',
					self::PAGE_SLUG
				)
			)
		);

		exit;
	}

	/**
	 * Render the custom dashboard template.
	 *
	 * @return void
	 */
	public function render_dashboard() {

		if ( ! current_user_can( self::CAPABILITY ) ) {
			wp_die(
				esc_html__(
					'You do not have permission to access this dashboard.',
					'goug-framework'
				)
			);
		}

		$dashboard_data = new Dashboard_Data();
		$data           = $dashboard_data->get_data();

		View::render(
			'dashboard/admin-dashboard',
			array(
				'data' => $data,
			)
		);
	}

	/**
	 * Add a dashboard-specific class to the admin body.
	 *
	 * @param string $classes Existing admin body classes.
	 *
	 * @return string
	 */
	public function add_dashboard_body_class( $classes ) {

		if ( $this->is_dashboard_screen() ) {
			$classes .= ' goug-dashboard-page';
		}

		return $classes;
	}

	/**
	 * Determine whether the current admin screen is the custom dashboard.
	 *
	 * @return bool
	 */
	private function is_dashboard_screen() {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return false;
		}

		$screen = get_current_screen();

		if ( ! $screen ) {
			return false;
		}

		return sprintf(
			'toplevel_page_%s',
			self::PAGE_SLUG
		) === $screen->id;
	}

	/**
	 * Preserve the existing admin-notice behavior.
	 *
	 * This is temporary and will be cleaned up when the dashboard
	 * template is split into components.
	 *
	 * @return void
	 */
	public function remove_admin_duplicates() {

		if ( ! $this->is_dashboard_screen() ) {
			return;
		}

		remove_all_actions( 'admin_notices' );
		do_action( 'admin_notices' );
	}

	/**
	 * Replace the WordPress admin footer message.
	 *
	 * This branding intentionally applies throughout the admin area.
	 *
	 * @return string
	 */
	public function custom_admin_footer() {

		global $wp_version;

		$theme = wp_get_theme();

		return sprintf(
			'<span id="goug-admin-footer">%1$s <strong>%2$s</strong> | %3$s: %4$s | %5$s: %6$s | %7$s ❤️</span>',
			esc_html__( 'Powered by', 'goug-framework' ),
			esc_html__( 'Goug Labs', 'goug-framework' ),
			esc_html__( 'WordPress', 'goug-framework' ),
			esc_html( $wp_version ),
			esc_html__( 'Theme', 'goug-framework' ),
			esc_html( $theme->get( 'Name' ) ),
			esc_html__( 'Made with', 'goug-framework' )
		);
	}

	/**
	 * Remove the native WordPress logo from the admin toolbar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WordPress admin toolbar instance.
	 *
	 * @return void
	 */
	public function remove_wp_logo( WP_Admin_Bar $wp_admin_bar ) {

		$wp_admin_bar->remove_node( 'wp-logo' );
	}

	/**
	 * Add the custom Goug Labs logo to the admin toolbar.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar WordPress admin toolbar instance.
	 *
	 * @return void
	 */
	public function add_custom_logo( WP_Admin_Bar $wp_admin_bar ) {

		$custom_icon_url = get_brand_logo_url( 'thumbnail' );
		$site_name       = get_bloginfo( 'name' );

		$wp_admin_bar->add_node(
			array(
				'id'    => 'custom-logo',
				'title' => sprintf(
					'<img src="%1$s" alt="%2$s" style="height:20px;width:auto;vertical-align:middle;">',
					esc_url( $custom_icon_url ),
					esc_attr(
						sprintf(
							/* translators: %s: Site name. */
							__( '%s logo', 'goug-framework' ),
							$site_name
						)
					)
				),
				'href'  => admin_url(
					sprintf(
						'admin.php?page=%s',
						self::PAGE_SLUG
					)
				),
				'meta'  => array(
					'title' => esc_attr(
						sprintf(
							/* translators: %s: Site name. */
							__( 'Go to the %s Dashboard', 'goug-framework' ),
							$site_name
						)
					),
				),
			)
		);
	}

	/**
	 * Register the optional admin dashboard widget area.
	 *
	 * @return void
	 */
	public function register_admin_widget_area() {

		register_sidebar(
			array(
				'name'          => __(
					'Admin Dashboard Widgets',
					'goug-framework'
				),
				'id'            => 'admin_dashboard_widgets',
				'description'   => __(
					'Widgets displayed on the custom WordPress dashboard.',
					'goug-framework'
				),
				'before_widget' => '<div class="admin-widget">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="widget-title">',
				'after_title'   => '</h3>',
			)
		);
	}
}