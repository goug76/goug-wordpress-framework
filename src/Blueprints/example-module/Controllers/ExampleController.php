<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Example\Controllers;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Example\Services\ExampleService;
use RuntimeException;

/**
 * Coordinates the Example module admin notice.
 */
final class ExampleController
{
    /**
     * Example feature service.
     */
    private ExampleService $service;

    /**
     * Absolute path to the notice view.
     */
    private string $viewPath;

    /**
     * Create the controller.
     */
    public function __construct(
        ExampleService $service,
        string $viewPath
    ) {
        $this->service = $service;
        $this->viewPath = $viewPath;
    }

    /**
     * Register WordPress hooks.
     */
    public function hooks(): void
    {
        add_action(
            'admin_notices',
            [$this, 'renderNotice']
        );
    }

    /**
     * Render the example admin notice.
     */
    public function renderNotice(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        if (! is_file($this->viewPath)) {
            throw new RuntimeException(
                sprintf(
                    'The Example module view could not be found at "%s".',
                    $this->viewPath
                )
            );
        }

        $heading = $this->service->heading();
        $message = $this->service->message();

        require $this->viewPath;
    }
}