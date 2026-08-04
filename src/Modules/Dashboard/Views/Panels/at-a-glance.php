<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Models\Statistic;

/**
 * At a Glance panel.
 *
 * @var list<Statistic> $statistics
 */
?>

<?php if ($statistics === []) : ?>
    <p class="goug-admin__muted">
        No published content was found.
    </p>
<?php else : ?>
    <ul class="goug-dashboard__stat-list">
        <?php foreach ($statistics as $statistic) : ?>
            <li class="goug-dashboard__stat">
                <span
                    class="dashicons <?php echo esc_attr($statistic->icon()); ?>"
                    aria-hidden="true"
                ></span>

                <span class="goug-dashboard__stat-value">
                    <?php
                    echo esc_html(
                        number_format_i18n(
                            $statistic->value()
                        )
                    );
                    ?>
                </span>

                <span class="goug-dashboard__stat-label">
                    <?php echo esc_html($statistic->label()); ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>