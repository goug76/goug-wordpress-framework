<?php
/**
 * Dashboard update data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides WordPress update information.
 */
class Update_Service {

	/**
	 * Cached update data for the current request.
	 *
	 * @var array|null
	 */
	private $update_data = null;

	/**
	 * Return available WordPress update counts.
	 *
	 * WordPress stores update information in transients, so this does
	 * not perform a remote request whenever the dashboard is loaded.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->update_data ) {
			return $this->update_data;
		}

		$update_data = wp_get_update_data();
		$counts      = isset( $update_data['counts'] )
			&& is_array( $update_data['counts'] )
				? $update_data['counts']
				: array();

		$this->update_data = array(
			'core' => isset( $counts['wordpress'] )
				? (int) $counts['wordpress']
				: 0,
			'plugins' => isset( $counts['plugins'] )
				? (int) $counts['plugins']
				: 0,
			'themes' => isset( $counts['themes'] )
				? (int) $counts['themes']
				: 0,
			'translations' => isset( $counts['translations'] )
				? (int) $counts['translations']
				: 0,
			'total' => isset( $counts['total'] )
				? (int) $counts['total']
				: 0,
		);

		return $this->update_data;
	}
}