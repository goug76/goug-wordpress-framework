<?php
/**
 * Dashboard content data service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides WordPress content and user counts.
 */
class Content_Service {

	/**
	 * Cached content counts for the current request.
	 *
	 * @var array|null
	 */
	private $content_counts = null;

	/**
	 * Return WordPress content and user counts.
	 *
	 * Uses native WordPress APIs and request-level caching.
	 *
	 * @return array
	 */
	public function get_data() {

		if ( null !== $this->content_counts ) {
			return $this->content_counts;
		}

		$post_counts    = wp_count_posts( 'post' );
		$page_counts    = wp_count_posts( 'page' );
		$comment_counts = wp_count_comments();
		$user_counts    = count_users();

		$this->content_counts = array(
			'posts' => array(
				'published' => isset( $post_counts->publish )
					? (int) $post_counts->publish
					: 0,
				'drafts'    => isset( $post_counts->draft )
					? (int) $post_counts->draft
					: 0,
			),
			'pages' => array(
				'published' => isset( $page_counts->publish )
					? (int) $page_counts->publish
					: 0,
				'drafts'    => isset( $page_counts->draft )
					? (int) $page_counts->draft
					: 0,
			),
			'comments' => array(
				'total'    => isset( $comment_counts->total_comments )
					? (int) $comment_counts->total_comments
					: 0,
				'approved' => isset( $comment_counts->approved )
					? (int) $comment_counts->approved
					: 0,
				'pending'  => isset( $comment_counts->moderated )
					? (int) $comment_counts->moderated
					: 0,
				'spam'     => isset( $comment_counts->spam )
					? (int) $comment_counts->spam
					: 0,
			),
			'users' => isset( $user_counts['total_users'] )
				? (int) $user_counts['total_users']
				: 0,
		);

		return $this->content_counts;
	}
}