<?php
/**
 * Dashboard storage usage service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides cached WordPress storage usage information.
 */
class Storage_Service {

	/**
	 * Storage transient key.
	 *
	 * @var string
	 */
	const CACHE_KEY = 'goug_dashboard_storage_usage';

	/**
	 * Storage cache duration.
	 *
	 * @var int
	 */
	const CACHE_DURATION = 12 * HOUR_IN_SECONDS;

	/**
	 * Database information service.
	 *
	 * @var Database_Service
	 */
	private $database_service;

	/**
	 * Request-level cache.
	 *
	 * @var array|null
	 */
	private $storage_data = null;

	/**
	 * Initialize the storage service.
	 *
	 * @param Database_Service $database_service Database service.
	 */
	public function __construct(
		Database_Service $database_service
	) {
		$this->database_service = $database_service;
	}

	/**
	 * Return cached storage usage information.
	 *
	 * @param bool $force_refresh Whether to recalculate storage.
	 *
	 * @return array
	 */
	public function get_data( $force_refresh = false ) {

		if (
			! $force_refresh &&
			null !== $this->storage_data
		) {
			return $this->storage_data;
		}

		if ( ! $force_refresh ) {
			$cached_data = get_transient( self::CACHE_KEY );

			if ( is_array( $cached_data ) ) {
				$this->storage_data = $cached_data;

				return $this->storage_data;
			}
		}

		$this->storage_data = $this->calculate_storage();

		set_transient(
			self::CACHE_KEY,
			$this->storage_data,
			self::CACHE_DURATION
		);

		return $this->storage_data;
	}

	/**
	 * Clear the cached storage result.
	 *
	 * @return void
	 */
	public function clear_cache() {

		$this->storage_data = null;

		delete_transient( self::CACHE_KEY );
	}

	/**
	 * Calculate WordPress storage usage.
	 *
	 * @return array
	 */
	private function calculate_storage() {

		$upload_data = wp_upload_dir();

		$uploads_path = ! empty( $upload_data['basedir'] )
			? $upload_data['basedir']
			: '';

		$plugins_path = defined( 'WP_PLUGIN_DIR' )
			? WP_PLUGIN_DIR
			: WP_CONTENT_DIR . '/plugins';

		$themes_path = get_theme_root();

		$wp_content_bytes = $this->get_directory_size(
			WP_CONTENT_DIR
		);

		$uploads_bytes = $this->get_directory_size(
			$uploads_path
		);

		$plugins_bytes = $this->get_directory_size(
			$plugins_path
		);

		$themes_bytes = $this->get_directory_size(
			$themes_path
		);

		$database_data = $this->database_service->get_data();

		$database_bytes = isset( $database_data['size_bytes'] )
			? (int) $database_data['size_bytes']
			: 0;

		/*
		 * Only subtract directories that actually live inside
		 * wp-content. WordPress allows these locations to be changed.
		 */
		$known_wp_content_bytes = 0;

		if (
			$this->is_path_within(
				$uploads_path,
				WP_CONTENT_DIR
			)
		) {
			$known_wp_content_bytes += $uploads_bytes;
		}

		if (
			$this->is_path_within(
				$plugins_path,
				WP_CONTENT_DIR
			)
		) {
			$known_wp_content_bytes += $plugins_bytes;
		}

		if (
			$this->is_path_within(
				$themes_path,
				WP_CONTENT_DIR
			)
		) {
			$known_wp_content_bytes += $themes_bytes;
		}

		$other_bytes = max(
			0,
			$wp_content_bytes - $known_wp_content_bytes
		);

		$total_bytes =
			$uploads_bytes
			+ $plugins_bytes
			+ $themes_bytes
			+ $other_bytes
			+ $database_bytes;

		$items = array(
			array(
				'id'          => 'uploads',
				'label'       => __( 'Uploads', 'goug-framework' ),
				'description' => __(
					'Media library files',
					'goug-framework'
				),
				'bytes'       => $uploads_bytes,
			),
			array(
				'id'          => 'database',
				'label'       => __( 'Database', 'goug-framework' ),
				'description' => __(
					'WordPress data and indexes',
					'goug-framework'
				),
				'bytes'       => $database_bytes,
			),
			array(
				'id'          => 'plugins',
				'label'       => __( 'Plugins', 'goug-framework' ),
				'description' => __(
					'Installed plugin files',
					'goug-framework'
				),
				'bytes'       => $plugins_bytes,
			),
			array(
				'id'          => 'themes',
				'label'       => __( 'Themes', 'goug-framework' ),
				'description' => __(
					'Installed theme files',
					'goug-framework'
				),
				'bytes'       => $themes_bytes,
			),
			array(
				'id'          => 'other',
				'label'       => __( 'Other Files', 'goug-framework' ),
				'description' => __(
					'Cache, languages, backups, and miscellaneous files',
					'goug-framework'
				),
				'bytes'       => $other_bytes,
			),
		);

		foreach ( $items as &$item ) {
			$item['formatted'] = size_format(
				$item['bytes'],
				1
			);

			$item['percentage'] = $total_bytes > 0
				? round(
					( $item['bytes'] / $total_bytes ) * 100,
					1
				)
				: 0;
		}

		unset( $item );

		$storage_data = array(
			'total_bytes'   => $total_bytes,
			'total'         => size_format( $total_bytes, 1 ),
			'items'         => $items,
			'calculated_at' => time(),
		);

		/**
		 * Filter dashboard storage usage information.
		 *
		 * @param array $storage_data Storage information.
		 */
		$storage_data = apply_filters(
			'goug_dashboard_storage_data',
			$storage_data
		);

		return is_array( $storage_data )
			? $storage_data
			: array();
	}

	/**
	 * Return the total size of a directory.
	 *
	 * Symbolic links and unreadable files are skipped.
	 *
	 * @param string $path Directory path.
	 *
	 * @return int
	 */
	private function get_directory_size( $path ) {

		if (
			empty( $path ) ||
			! is_dir( $path ) ||
			! is_readable( $path )
		) {
			return 0;
		}

		$total_bytes = 0;

		try {
			$directory = new \RecursiveDirectoryIterator(
				$path,
				\FilesystemIterator::SKIP_DOTS
			);

			$iterator = new \RecursiveIteratorIterator(
				$directory,
				\RecursiveIteratorIterator::LEAVES_ONLY
			);

			foreach ( $iterator as $file ) {
				if (
					! $file instanceof \SplFileInfo ||
					! $file->isFile() ||
					$file->isLink()
				) {
					continue;
				}

				try {
					$total_bytes += $file->getSize();
				} catch ( \RuntimeException $exception ) {
					continue;
				}
			}
		} catch ( \Throwable $exception ) {
			/*
			 * Return whatever was calculated before the inaccessible
			 * directory or file was encountered.
			 */
		}

		return $total_bytes;
	}

	/**
	 * Determine whether one path is inside another.
	 *
	 * @param string $path        Child path.
	 * @param string $parent_path Parent path.
	 *
	 * @return bool
	 */
	private function is_path_within(
		$path,
		$parent_path
	) {
		if ( empty( $path ) || empty( $parent_path ) ) {
			return false;
		}

		$normalized_path = wp_normalize_path(
			realpath( $path ) ?: $path
		);

		$normalized_parent = trailingslashit(
			wp_normalize_path(
				realpath( $parent_path ) ?: $parent_path
			)
		);

		return 0 === strpos(
			trailingslashit( $normalized_path ),
			$normalized_parent
		);
	}
}