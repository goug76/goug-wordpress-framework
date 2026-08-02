<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Contracts;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Runtime;

/**
 * Defines the lifecycle behavior of a module provider.
 *
 * Providers organize related module responsibilities while remaining
 * internal to the module that owns them.
 */
interface ProviderContract
{
    /**
     * Register definitions and infrastructure.
     */
    public function register(Runtime $runtime): void;

    /**
     * Activate runtime behavior.
     */
    public function boot(Runtime $runtime): void;
}