<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;

/**
 * Represents the running state of GOUG Framework.
 *
 * Runtime owns shared Core infrastructure for the lifetime of the
 * current WordPress request.
 */
final class Runtime
{
    /**
     * Framework installation configuration.
     */
    private Configuration $configuration;

    /**
     * Create the framework runtime.
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Return the framework configuration.
     */
    public function configuration(): Configuration
    {
        return $this->configuration;
    }
}