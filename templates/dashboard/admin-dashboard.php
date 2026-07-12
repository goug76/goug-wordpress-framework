<?php
/**
 * Custom Admin Dashboard Template.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="wrap goug-dashboard">
	<div class="goug-dashboard__shell">

		<?php
		\GOUG\Inc\View::render(
            'dashboard/components/header',
            array(
                'site_name' => $data['site']['name'],
                'user'      => $data['user'],
            )
        );
		?>
        
		<main class="goug-dashboard__workspace">

			<div class="goug-dashboard__grid">

                <?php foreach ( $data['panels'] as $panel ) : ?>

                    <?php
                    if ( ! is_array( $panel ) ) {
                        continue;
                    }

                    \GOUG\Inc\View::render(
                        'components/panel',
                        array(
                            'title'       => $panel['title'] ?? '',
                            'icon'        => $panel['icon'] ?? '',
                            'class_name'  => $panel['class_name'] ?? '',
                            'body_view'   => $panel['body_view'] ?? '',
                            'body_data'   => $panel['body_data'] ?? array(),
                            'attributes'  => $panel['attributes'] ?? array(),
                        )
                    );
                    ?>

                <?php endforeach; ?>

			</div>

		</main>

	</div>
</div>