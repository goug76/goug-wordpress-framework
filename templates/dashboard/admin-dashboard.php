<?php
/**
 * Custom Admin Dashboard Template.
 *
 * @package GOUG
 */

defined( 'ABSPATH' ) || exit;

$preferences = isset( $data['preferences'] )
	&& is_array( $data['preferences'] )
		? $data['preferences']
		: array();

$density = isset( $preferences['density'] )
	? sanitize_key( $preferences['density'] )
	: 'comfortable';

$allowed_densities = array(
	'compact',
	'comfortable',
	'spacious',
);

if ( ! in_array( $density, $allowed_densities, true ) ) {
	$density = 'comfortable';
}

$dashboard_classes = array(
	'wrap',
	'goug-dashboard',
	'goug-dashboard--density-' . $density,
);

if ( empty( $preferences['enable_motion'] ) ) {
	$dashboard_classes[] = 'goug-dashboard--motion-disabled';
}

$dashboard_class_attribute = implode(
	' ',
	array_map(
		'sanitize_html_class',
		$dashboard_classes
	)
);
?>

<div class="<?php echo esc_attr( $dashboard_class_attribute ); ?>">
	<div class="goug-dashboard__shell">

		<?php
		\GOUG\Inc\View::render(
            'dashboard/components/header',
            array(
                'site_name'     => $data['site']['name'] ?? '',
                'user'          => $data['user'] ?? array(),
                'show_greeting' => ! empty(
                    $preferences['show_greeting']
                ),
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
                            'title'      => $panel['title'] ?? '',
                            'icon'       => $panel['icon'] ?? '',
                            'icon_svg'   => $panel['icon_svg'] ?? '',
                            'class_name' => $panel['class_name'] ?? '',
                            'body_view'  => $panel['body_view'] ?? '',
                            'body_data'  => $panel['body_data'] ?? array(),
                            'attributes' => $panel['attributes'] ?? array(),
                            'collapsed'  => ! empty( $panel['collapsed'] ),
                        )
                    );
                    ?>

                <?php endforeach; ?>

			</div>

		</main>

	</div>
</div>