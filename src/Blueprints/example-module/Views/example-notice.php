<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * Example module admin notice.
 *
 * Available variables:
 *
 * @var string $heading
 * @var string $message
 */
?>

<div class="notice notice-success is-dismissible">
    <p>
        <strong>
            <?php echo esc_html($heading); ?>
        </strong>
    </p>

    <p>
        <?php echo esc_html($message); ?>
    </p>
</div>