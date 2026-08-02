<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Example\Providers;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Runtime;

/**
 * Coordinates Example module infrastructure and runtime behavior.
 */
final class ExampleProvider implements ProviderContract
{
    /**
     * Register module definitions and infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        // Register services and module infrastructure here.
    }

    /**
     * Activate module runtime behavior.
     */
    public function boot(Runtime $runtime): void
    {
        // Register WordPress hooks and runtime behavior here.
    }
}