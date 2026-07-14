<?php
/**
 * Dashboard activity data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides recent native WordPress activity.
 */
class Activity_Service {

	/**
	 * Cached activity for the current request.
	 *
	 * @var array|null
	 */
	private $activity_items = null;

	/**
	 * Maximum number of final activity items.
	 *
	 * @var int
	 */
	private $limit;

	/**
	 * Initialize the activity service.
	 *
	 * @param int $limit Maximum number of returned items.
	 */
	public function __construct( $limit = 6 ) {

		$this->limit = max( 1, absint( $limit ) );
	}

	/**
	 * Return recent WordPress activity.
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

		/*
		 * Sort all activity types together using their Unix timestamp.
		 */
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

		$items = array_slice(
			$items,
			0,
			$this->limit
		);

		/**
		 * Filter Recent Activity items.
		 *
		 * Integrations may add normalized activity entries here.
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
	 * Return recent post and page activity.
	 *
	 * @return array
	 */
	private function get_content_activity() {

		$query = new \WP_Query(
			array(
				'post_type'              => array( 'post', 'page' ),
				'post_status'            => array( 'publish', 'draft' ),
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
				! $post instanceof \WP_Post ||
				! current_user_can( 'edit_post', $post->ID )
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

			/*
			 * New posts commonly have matching or nearly matching
			 * published and modified timestamps.
			 */
			$is_newly_published = (
				'publish' === $post->post_status &&
				abs(
					$modified_timestamp - $published_timestamp
				) <= 60
			);

			if ( 'draft' === $post->post_status ) {
				$type  = 'draft';
				$state = 'neutral';
				$icon  = 'dashicons-edit';
				$text  = __( 'Saved draft', 'goug-framework' );
			} elseif ( $is_newly_published ) {
				$type  = 'published';
				$state = 'success';
				$icon  = 'dashicons-yes-alt';
				$text  = __( 'Published', 'goug-framework' );
			} else {
				$type  = 'updated';
				$state = 'info';
				$icon  = 'dashicons-update';
				$text  = __( 'Updated', 'goug-framework' );
			}

            $post_type_object = get_post_type_object(
                $post->post_type
            );

            $post_type_label = (
                $post_type_object &&
                ! empty( $post_type_object->labels->singular_name )
            )
                ? $post_type_object->labels->singular_name
                : __( 'Content', 'goug-framework' );

			$title = get_the_title( $post );

			if ( '' === trim( $title ) ) {
				$title = __( '(no title)', 'goug-framework' );
			}

			$items[] = array(
				'id'        => 'content-' . $post->ID,
				'type'      => $type,
				'state'     => $state,
				'icon'      => $icon,
				'action' => sprintf(
                    /* translators: 1: Activity action. 2: Content type. */
                    __( '%1$s %2$s', 'goug-framework' ),
                    $text,
                    $post_type_label
                ),
				'title'     => $title,
				'timestamp' => $modified_timestamp,
				'url'       => get_edit_post_link(
					$post->ID,
					'raw'
				),
			);
		}

		return $items;
	}

	/**
	 * Return recent comment activity.
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
				$post_title = __( '(no title)', 'goug-framework' );
			}

			$author = $comment->comment_author
				? $comment->comment_author
				: __( 'Anonymous', 'goug-framework' );

			$is_pending = '0' === (string) $comment->comment_approved;

			$items[] = array(
				'id'        => 'comment-' . $comment->comment_ID,
				'type'      => 'comment',
				'state'     => $is_pending ? 'warning' : 'comment',
				'icon'      => 'dashicons-admin-comments',
				'action'    => $is_pending
					? __( 'Pending comment', 'goug-framework' )
					: __( 'New comment', 'goug-framework' ),
				'title'     => sprintf(
					/* translators: 1: Comment author. 2: Post title. */
					__( '%1$s on “%2$s”', 'goug-framework' ),
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
			);
		}

		return $items;
	}

	/**
	 * Return recently registered users.
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

			$items[] = array(
				'id'        => 'user-' . $user->ID,
				'type'      => 'user',
				'state'     => 'user',
				'icon'      => 'dashicons-admin-users',
				'action'    => __( 'User registered', 'goug-framework' ),
				'title'     => $user->display_name,
				'timestamp' => mysql2date(
					'U',
					$user->user_registered,
					false
				),
				'url'       => get_edit_user_link( $user->ID ),
			);
		}

		return $items;
	}
}