<?php
/**
 * Dashboard panel contract.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Panels;

defined( 'ABSPATH' ) || exit;

use GOUG\Inc\Dashboard\Dashboard_Registry;

interface Dashboard_Panel {

	/**
	 * Register the panel with the dashboard registry.
	 *
	 * @param Dashboard_Registry $registry Dashboard panel registry.
	 *
	 * @return void
	 */
	public function register( Dashboard_Registry $registry );
}