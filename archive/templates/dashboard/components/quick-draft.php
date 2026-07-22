<?php
/**
 * Dashboard Quick Draft component.
 *
 * @var string $ajax_action AJAX action name.
 * @var string $nonce       Request nonce.
 * @var array  $categories  Available post categories.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$ajax_action = isset( $ajax_action )
	? (string) $ajax_action
	: '';

$nonce = isset( $nonce )
	? (string) $nonce
	: '';

$categories = isset( $categories ) && is_array( $categories )
	? $categories
	: array();

if (
	'' === $ajax_action ||
	'' === $nonce
) {
	return;
}
?>

<form
	class="goug-quick-draft"
	data-quick-draft-form
>
	<input
		type="hidden"
		name="action"
		value="<?php echo esc_attr( $ajax_action ); ?>"
	>

	<input
		type="hidden"
		name="nonce"
		value="<?php echo esc_attr( $nonce ); ?>"
	>

	<div class="goug-quick-draft__field">
		<label for="goug-quick-draft-title">
			<?php esc_html_e( 'Title', 'goug-framework' ); ?>
		</label>

		<input
			id="goug-quick-draft-title"
			name="title"
			type="text"
			autocomplete="off"
			required
			placeholder="<?php
			echo esc_attr__(
				'What are you working on?',
				'goug-framework'
			);
			?>"
		>
	</div>

	<div class="goug-quick-draft__field">
		<div class="goug-quick-draft__label-row">
			<label for="goug-quick-draft-content">
				<?php esc_html_e( 'Content', 'goug-framework' ); ?>
			</label>

			<span
				class="goug-quick-draft__counter"
				data-quick-draft-counter
			>
				0 / 1000
			</span>
		</div>

		<textarea
			id="goug-quick-draft-content"
			name="content"
			rows="9"
			maxlength="1000"
			placeholder="<?php
			echo esc_attr__(
				'Start writing...',
				'goug-framework'
			);
			?>"
			data-quick-draft-content
		></textarea>
	</div>

	<?php if ( ! empty( $categories ) ) : ?>
		<div class="goug-quick-draft__field">
			<label for="goug-quick-draft-category">
				<?php esc_html_e( 'Category', 'goug-framework' ); ?>
			</label>

			<select
				id="goug-quick-draft-category"
				name="category"
			>
				<option value="0">
					<?php
					esc_html_e(
						'Use default category',
						'goug-framework'
					);
					?>
				</option>

				<?php foreach ( $categories as $category ) : ?>
					<option
						value="<?php
						echo esc_attr( $category->term_id );
						?>"
					>
						<?php echo esc_html( $category->name ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	<?php endif; ?>

	<div
		class="goug-quick-draft__notice"
		data-quick-draft-notice
		role="status"
		aria-live="polite"
		hidden
	></div>

	<div class="goug-quick-draft__actions">
		<button
			class="goug-quick-draft__submit"
			type="submit"
		>
			<span
				class="dashicons dashicons-saved"
				aria-hidden="true"
			></span>

			<span data-quick-draft-button-text>
				<?php esc_html_e( 'Save Draft', 'goug-framework' ); ?>
			</span>
		</button>
	</div>
</form>