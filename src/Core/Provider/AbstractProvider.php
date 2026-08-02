<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Provider;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Runtime;

/**
 * Provides a base implementation for framework providers.
 *
 * Providers may override only the lifecycle methods they require.
 */
abstract class AbstractProvider implements ProviderContract
{
    /**
     * Register infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        // Default implementation.
    }

    /**
     * Boot runtime behavior.
     */
    public function boot(Runtime $runtime): void
    {
        // Default implementation.
    }
}