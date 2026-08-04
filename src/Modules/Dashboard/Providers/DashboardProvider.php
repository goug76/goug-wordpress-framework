<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Providers;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Runtime;
use Goug\Framework\Modules\Dashboard\Controllers\DashboardController;
use LogicException;

/**
 * Wires the Dashboard module.
 */
final class DashboardProvider implements ProviderContract
{
    private ?DashboardController $controller = null;

    public function register(Runtime $runtime): void
    {
        $viewPath = $runtime
            ->configuration()
            ->path(
                'src/Modules/Dashboard/Views/dashboard.php'
            );

        $this->controller = new DashboardController(
            $viewPath
        );
    }

    public function boot(Runtime $runtime): void
    {
        if ($this->controller === null) {
            throw new LogicException(
                'The Dashboard module must be registered before it is booted.'
            );
        }

        $this->controller->hooks();
    }
}