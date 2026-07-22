<?php
/**
 * Dashboard activity data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides normalized recent WordPress activity for the dashboard.
 *
 * Responsibilities:
 *
 * - Collect recent content, comment, and user activity.
 * - Normalize different activity sources into one consistent structure.
 * - Merge and sort activity by timestamp.
 * - Limit the final activity feed to the configured number of items.
 * - Provide an extension point for additional activity integrations.
 *
 * This service prepares data only. It does not register dashboard
 * panels or render presentation markup.
 */
class Activity_Service {

	/**
	 * Cached activity for the current request.
	 *
	 * Prevents activity queries from running more than once during a
	 * single WordPress request.
	 *
	 * @var array|null
	 */
	private $activity_items = null;

	/**
	 * Maximum number of final activity items.
	 *
	 * Each activity source may return up to this number before all
	 * sources are merged, sorted, and reduced to the final limit.
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * Initialize the activity service.
	 *
	 * @param int $limit Maximum number of returned activity items.
	 */
	public function __construct( $limit = 6 ) {

		$this->limit = max(
			1,
			absint( $limit )
		);
	}

	/**
	 * Return recent normalized WordPress activity.
	 *
	 * Activity from all supported sources is merged and sorted before
	 * the final item limit is applied.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->activity_items ) {
			return $this->activity_items;
		}

		$items = array_merge(
			$this->get_content_activity(),
			$this->get_comment_activity(),
			$this->get_user_activity()
		);

		$items = $this->sort_activity_items(
			$items
		);

		$items = array_slice(
			$items,
			0,
			$this->limit
		);

		/**
		 * Filter Recent Activity items.
		 *
		 * Integrations may modify the normalized activity entries
		 * returned by the default WordPress activity providers.
		 *
		 * @param array $items Recent activity items.
		 */
		$items = apply_filters(
			'goug_dashboard_activity_items',
			$items
		);

		$this->activity_items = is_array( $items )
			? array_values( $items )
			: array();

		return $this->activity_items;
	}

	/**
	 * Collect recently published, updated, and drafted content.
	 *
	 * Posts and pages are converted into normalized activity items so
	 * they can be merged with comment and user activity.
	 *
	 * @return array
	 */
	private function get_content_activity() {

		$query = new \WP_Query(
			array(
				'post_type'              => array(
					'post',
					'page',
				),
				'post_status'            => array(
					'publish',
					'draft',
				),
				'posts_per_page'         => $this->limit,
				'orderby'                => 'modified',
				'order'                  => 'DESC',
				'ignore_sticky_posts'    => true,
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		$items = array();

		foreach ( $query->posts as $post ) {

			if (
				! $post instanceof \WP_Post
				|| ! current_user_can(
					'edit_post',
					$post->ID
				)
			) {
				continue;
			}

			$published_timestamp = get_post_time(
				'U',
				true,
				$post
			);

			$modified_timestamp = get_post_modified_time(
				'U',
				true,
				$post
			);

			$status = $this->get_content_activity_status(
				$post,
				$published_timestamp,
				$modified_timestamp
			);

			$post_type_label = $this->get_post_type_label(
				$post->post_type
			);

			$title = $this->get_content_title(
				$post
			);

			$items[] = $this->build_activity_item(
				array(
					'id'        => 'content-' . $post->ID,
					'type'      => $status['type'],
					'state'     => $status['state'],
					'icon'      => $status['icon'],
					'action'    => sprintf(
						/* translators: 1: Activity action. 2: Content type. */
						__(
							'%1$s %2$s',
							'goug-framework'
						),
						$status['label'],
						$post_type_label
					),
					'title'     => $title,
					'timestamp' => $modified_timestamp,
					'url'       => get_edit_post_link(
						$post->ID,
						'raw'
					),
				)
			);
		}

		return $items;
	}

	/**
	 * Collect recent comment activity.
	 *
	 * Comment activity is available only to users who can moderate
	 * comments. Pending comments receive a warning state.
	 *
	 * @return array
	 */
	private function get_comment_activity() {

		if ( ! current_user_can( 'moderate_comments' ) ) {
			return array();
		}

		$comments = get_comments(
			array(
				'number'  => $this->limit,
				'orderby' => 'comment_date_gmt',
				'order'   => 'DESC',
				'status'  => 'all',
				'type'    => 'comment',
			)
		);

		$items = array();

		foreach ( $comments as $comment ) {

			if ( ! $comment instanceof \WP_Comment ) {
				continue;
			}

			$post_title = get_the_title(
				$comment->comment_post_ID
			);

			if ( '' === trim( $post_title ) ) {
				$post_title = __(
					'(no title)',
					'goug-framework'
				);
			}

			$author = $comment->comment_author
				? $comment->comment_author
				: __( 'Anonymous', 'goug-framework' );

			$is_pending = '0'
				=== (string) $comment->comment_approved;

			$items[] = $this->build_activity_item(
				array(
					'id'        => 'comment-'
						. $comment->comment_ID,
					'type'      => 'comment',
					'state'     => $is_pending
						? 'warning'
						: 'comment',
					'icon'      => 'dashicons-admin-comments',
					'action'    => $is_pending
						? __(
							'Pending comment',
							'goug-framework'
						)
						: __(
							'New comment',
							'goug-framework'
						),
					'title'     => sprintf(
						/* translators: 1: Comment author. 2: Post title. */
						__(
							'%1$s on “%2$s”',
							'goug-framework'
						),
						$author,
						$post_title
					),
					'timestamp' => mysql2date(
						'U',
						$comment->comment_date_gmt,
						false
					),
					'url'       => get_edit_comment_link(
						$comment->comment_ID
					),
				)
			);
		}

		return $items;
	}

	/**
	 * Collect recently registered WordPress users.
	 *
	 * User activity is returned only when the current administrator
	 * has permission to list users.
	 *
	 * @return array
	 */
	private function get_user_activity() {

		if ( ! current_user_can( 'list_users' ) ) {
			return array();
		}

		$users = get_users(
			array(
				'number'  => $this->limit,
				'orderby' => 'registered',
				'order'   => 'DESC',
				'fields'  => array(
					'ID',
					'display_name',
					'user_registered',
				),
			)
		);

		$items = array();

		foreach ( $users as $user ) {

			if ( ! $user instanceof \WP_User ) {
				continue;
			}

			$items[] = $this->build_activity_item(
				array(
					'id'        => 'user-' . $user->ID,
					'type'      => 'user',
					'state'     => 'user',
					'icon'      => 'dashicons-admin-users',
					'action'    => __(
						'User registered',
						'goug-framework'
					),
					'title'     => $user->display_name,
					'timestamp' => mysql2date(
						'U',
						$user->user_registered,
						false
					),
					'url'       => get_edit_user_link(
						$user->ID
					),
				)
			);
		}

		return $items;
	}

	/**
	 * Determine the activity state for a post or page.
	 *
	 * Newly published content commonly has publication and modification
	 * timestamps within a few seconds of each other. A sixty-second
	 * tolerance prevents it from being incorrectly labeled as updated.
	 *
	 * @param \WP_Post $post                Content object.
	 * @param int      $published_timestamp Publication timestamp.
	 * @param int      $modified_timestamp  Modification timestamp.
	 *
	 * @return array
	 */
	private function get_content_activity_status(
		$post,
		$published_timestamp,
		$modified_timestamp
	) {

		$is_newly_published = (
			'publish' === $post->post_status
			&& abs(
				$modified_timestamp
				- $published_timestamp
			) <= 60
		);

		if ( 'draft' === $post->post_status ) {
			return array(
				'type'  => 'draft',
				'state' => 'neutral',
				'icon'  => 'dashicons-edit',
				'label' => __(
					'Saved draft',
					'goug-framework'
				),
			);
		}

		if ( $is_newly_published ) {
			return array(
				'type'  => 'published',
				'state' => 'success',
				'icon'  => 'dashicons-yes-alt',
				'label' => __(
					'Published',
					'goug-framework'
				),
			);
		}

		return array(
			'type'  => 'updated',
			'state' => 'info',
			'icon'  => 'dashicons-update',
			'label' => __(
				'Updated',
				'goug-framework'
			),
		);
	}

	/**
	 * Return a readable singular label for a post type.
	 *
	 * @param string $post_type Post type name.
	 *
	 * @return string
	 */
	private function get_post_type_label( $post_type ) {

		$post_type_object = get_post_type_object(
			$post_type
		);

		if (
			$post_type_object
			&& ! empty(
				$post_type_object->labels->singular_name
			)
		) {
			return $post_type_object->labels->singular_name;
		}

		return __( 'Content', 'goug-framework' );
	}

	/**
	 * Return a safe display title for a post or page.
	 *
	 * @param \WP_Post $post Content object.
	 *
	 * @return string
	 */
	private function get_content_title( $post ) {

		$title = get_the_title( $post );

		return '' !== trim( $title )
			? $title
			: __( '(no title)', 'goug-framework' );
	}

	/**
	 * Build a normalized dashboard activity item.
	 *
	 * All activity providers use this structure so their results can be
	 * merged, sorted, filtered, and rendered consistently.
	 *
	 * @param array $activity Activity definition.
	 *
	 * @return array
	 */
	private function build_activity_item( array $activity ) {

		$item = wp_parse_args(
			$activity,
			array(
				'id'        => '',
				'type'      => '',
				'state'     => 'neutral',
				'icon'      => 'dashicons-marker',
				'action'    => '',
				'title'     => '',
				'timestamp' => 0,
				'url'       => '',
			)
		);

		$item['id']        = sanitize_key( $item['id'] );
		$item['type']      = sanitize_key( $item['type'] );
		$item['state']     = sanitize_key( $item['state'] );
		$item['icon']      = sanitize_html_class(
			$item['icon']
		);
		$item['action']    = (string) $item['action'];
		$item['title']     = (string) $item['title'];
		$item['timestamp'] = max(
			0,
			(int) $item['timestamp']
		);
		$item['url']       = (string) $item['url'];

		return $item;
	}

	/**
	 * Sort activity items from newest to oldest.
	 *
	 * @param array $items Activity items.
	 *
	 * @return array
	 */
	private function sort_activity_items( $items ) {

		if ( ! is_array( $items ) ) {
			return array();
		}

		usort(
			$items,
			static function ( $first, $second ) {

				$first_time = isset( $first['timestamp'] )
					? (int) $first['timestamp']
					: 0;

				$second_time = isset( $second['timestamp'] )
					? (int) $second['timestamp']
					: 0;

				return $second_time <=> $first_time;
			}
		);

		return $items;
	}
}