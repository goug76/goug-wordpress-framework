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
    /**
     * Boot the framework application.
     */
    public function boot(): void
    {
        $lifecycle = new Lifecycle();

        $lifecycle->run();
    }
}