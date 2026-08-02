<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Lifecycle\Lifecycle;

/**
 * Coordinates the running GOUG Framework application.
 */
final class Application
{
    private Runtime $runtime;

    public function __construct()
    {
        $this->runtime = new Runtime();
    }

    /**
     * Boot the framework application.
     */
    public function boot(): void
    {
        $lifecycle = new Lifecycle($this->runtime);

        $lifecycle->run();
    }

    /**
     * Get the current runtime.
     */
    public function runtime(): Runtime
    {
        return $this->runtime;
    }
}