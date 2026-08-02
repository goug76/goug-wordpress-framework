<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;
use Goug\Framework\Core\Lifecycle\Lifecycle;

/**
 * Coordinates the running GOUG Framework application.
 */
final class Application
{
    /**
     * Current framework runtime.
     */
    private Runtime $runtime;

    /**
     * Create the framework application.
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->runtime = new Runtime(
            $configuration
        );
    }

    /**
     * Boot the framework application.
     */
    public function boot(): void
    {
        $lifecycle = new Lifecycle(
            $this->runtime
        );

        $lifecycle->run();
    }

    /**
     * Return the current framework runtime.
     */
    public function runtime(): Runtime
    {
        return $this->runtime;
    }
}