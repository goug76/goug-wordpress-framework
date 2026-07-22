<?php
/**
 * Dashboard Quick Draft service.
 *
 * @package GOUG
 */

namespace GOUG\Inc\Dashboard\Services;

defined( 'ABSPATH' ) || exit;

/**
 * Provides Quick Draft form data and handles draft creation.
 */
class Draft_Service {

	/**
	 * AJAX action name.
	 *
	 * @var string
	 */
	const AJAX_ACTION = 'goug_save_quick_draft';

	/**
	 * Nonce action.
	 *
	 * @var string
	 */
	const NONCE_ACTION = 'goug_quick_draft';

	/**
	 * Register service hooks.
	 *
	 * This must run on every authenticated admin request, including
	 * admin-ajax.php requests.
	 *
	 * @return void
	 */
	public function register_hooks() {

		add_action(
			'wp_ajax_' . self::AJAX_ACTION,
			array( $this, 'save_draft' )
		);
	}

	/**
	 * Return data needed by the Quick Draft form.
	 *
	 * @return array
	 */
	public function get_form_data() {

		$categories = get_categories(
			array(
				'hide_empty' => false,
				'orderby'    => 'name',
				'order'      => 'ASC',
			)
		);

		return array(
			'ajax_action' => self::AJAX_ACTION,
			'nonce'       => wp_create_nonce( self::NONCE_ACTION ),
			'categories'  => is_array( $categories )
				? $categories
				: array(),
			'can_create'  => current_user_can( 'edit_posts' ),
		);
	}

	/**
	 * Save a new post draft.
	 *
	 * @return void
	 */
	public function save_draft() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error(
				array(
					'message' => __(
						'You do not have permission to create drafts.',
						'goug-framework'
					),
				),
				403
			);
		}

		check_ajax_referer(
			self::NONCE_ACTION,
			'nonce'
		);

		$title = isset( $_POST['title'] )
			? sanitize_text_field(
				wp_unslash( $_POST['title'] )
			)
			: '';

		$content = isset( $_POST['content'] )
			? wp_kses_post(
				wp_unslash( $_POST['content'] )
			)
			: '';

		$category_id = isset( $_POST['category'] )
			? absint( $_POST['category'] )
			: 0;

		if ( '' === $title ) {
			wp_send_json_error(
				array(
					'message' => __(
						'Please enter a title for the draft.',
						'goug-framework'
					),
				),
				400
			);
		}

		$post_data = array(
			'post_type'    => 'post',
			'post_status'  => 'draft',
			'post_title'   => $title,
			'post_content' => $content,
			'post_author'  => get_current_user_id(),
		);

		if (
			$category_id > 0 &&
			term_exists( $category_id, 'category' )
		) {
			$post_data['post_category'] = array(
				$category_id,
			);
		}

		$post_id = wp_insert_post(
			$post_data,
			true
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error(
				array(
					'message' => __(
						'WordPress was unable to save the draft.',
						'goug-framework'
					),
				),
				500
			);
		}

		wp_send_json_success(
			array(
				'message'  => __(
					'Draft saved successfully.',
					'goug-framework'
				),
				'post_id'  => (int) $post_id,
				'edit_url' => get_edit_post_link(
					$post_id,
					'raw'
				),
			)
		);
	}
}