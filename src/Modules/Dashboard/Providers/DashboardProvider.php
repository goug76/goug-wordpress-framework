<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Providers;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Provider\AbstractProvider;
use Goug\Framework\Core\Runtime;

/**
 * Coordinates the primary Dashboard module infrastructure.
 */
final class DashboardProvider extends AbstractProvider
{
    /**
     * Register Dashboard infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        // Dashboard infrastructure will be registered when required.
    }

    /**
     * Activate Dashboard runtime behavior.
     */
    public function boot(Runtime $runtime): void
    {
        if (
            defined('WP_DEBUG')
            && WP_DEBUG
        ) {
            error_log('GOUG DashboardProvider booted.');
        }
    }
}