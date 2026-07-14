<?php
/**
 * Dashboard database information service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides lightweight WordPress database information.
 */
class Database_Service {

	/**
	 * Cached database data for the current request.
	 *
	 * @var array|null
	 */
	private $database_data = null;

	/**
	 * Return database information.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->database_data ) {
			return $this->database_data;
		}

		global $wpdb;

		$server_info = (string) $wpdb->db_server_info();
		$version     = (string) $wpdb->db_version();

		/*
		 * Only inspect tables belonging to this WordPress installation.
		 */
		$table_pattern = $wpdb->esc_like(
			$wpdb->prefix
		) . '%';

		$table_rows = $wpdb->get_results(
			$wpdb->prepare(
				'SHOW TABLE STATUS LIKE %s',
				$table_pattern
			)
		);

		$table_count = 0;
		$total_bytes = 0;

		if ( is_array( $table_rows ) ) {

			$table_count = count( $table_rows );

			foreach ( $table_rows as $table_row ) {

				if ( ! is_object( $table_row ) ) {
					continue;
				}

				$data_length = isset( $table_row->Data_length )
					? (int) $table_row->Data_length
					: 0;

				$index_length = isset( $table_row->Index_length )
					? (int) $table_row->Index_length
					: 0;

				$total_bytes += $data_length + $index_length;
			}
		}

		$this->database_data = array(
			'server'      => $this->get_server_name(
				$server_info
			),
			'version'     => $version,
			'server_info' => $server_info,
			'table_count' => $table_count,
			'size_bytes'  => $total_bytes,
			'size'        => size_format(
				$total_bytes,
				1
			),
		);

		/**
		 * Filter dashboard database information.
		 *
		 * @param array $database_data Database information.
		 */
		$this->database_data = apply_filters(
			'goug_dashboard_database_data',
			$this->database_data
		);

		return is_array( $this->database_data )
			? $this->database_data
			: array();
	}

	/**
	 * Determine the database server name.
	 *
	 * @param string $server_info Raw database server information.
	 *
	 * @return string
	 */
	private function get_server_name( $server_info ) {

		if ( false !== stripos( $server_info, 'mariadb' ) ) {
			return 'MariaDB';
		}

		if ( false !== stripos( $server_info, 'percona' ) ) {
			return 'Percona';
		}

		if ( false !== stripos( $server_info, 'mysql' ) ) {
			return 'MySQL';
		}

		return __( 'Database', 'goug-framework' );
	}
}