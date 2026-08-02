<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Example\Providers;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Runtime;
use Goug\Framework\Modules\Example\Controllers\ExampleController;
use Goug\Framework\Modules\Example\Services\ExampleService;

/**
 * Wires the Example module dependencies and runtime behavior.
 */
final class ExampleProvider implements ProviderContract
{
    /**
     * Example module controller.
     */
    private ?ExampleController $controller = null;

    /**
     * Register Example module infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        $service = new ExampleService();

        $viewPath = $runtime
            ->configuration()
            ->path(
                'src/Modules/Example/Views/example-notice.php'
            );

        $this->controller = new ExampleController(
            $service,
            $viewPath
        );
    }

    /**
     * Activate Example module behavior.
     */
    public function boot(Runtime $runtime): void
    {
        if ($this->controller === null) {
            throw new \LogicException(
                'The Example module must be registered before it is booted.'
            );
        }

        $this->controller->hooks();
    }
}