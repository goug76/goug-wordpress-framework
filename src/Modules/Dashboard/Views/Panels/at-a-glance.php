<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * At a Glance panel.
 *
 * @var list<array{
 *     label: string,
 *     count: int,
 *     icon: string
 * }> $contentCounts
 */
?>

<?php if ($contentCounts === []) : ?>
    <p class="goug-admin__muted">
        No published content was found.
    </p>
<?php else : ?>
    <ul class="goug-dashboard__stat-list">
        <?php foreach ($contentCounts as $item) : ?>
            <li class="goug-dashboard__stat">
                <span
                    class="dashicons <?php echo esc_attr($item['icon']); ?>"
                    aria-hidden="true"
                ></span>

                <span class="goug-dashboard__stat-value">
                    <?php echo esc_html(
                        number_format_i18n(
                            $item['count']
                        )
                    ); ?>
                </span>

                <span class="goug-dashboard__stat-label">
                    <?php echo esc_html($item['label']); ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>