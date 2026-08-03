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

    private string $pageHook = '';

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
            'admin_menu',
            [$this, 'registerPage']
        );

        add_filter(
            'goug_framework_admin_ui_should_enqueue',
            [$this, 'shouldLoadAdminUi'],
            10,
            2
        );
    }

    /**
     * Render the Admin UI showcase page.
     */
    public function renderPage(): void
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

    /**
     * Register the Example module administration page.
     */
    public function registerPage(): void
    {
        $this->pageHook = add_menu_page(
            'GOUG Admin UI',
            'GOUG UI Test',
            'manage_options',
            'goug-ui-test',
            [$this, 'renderPage'],
            'dashicons-admin-appearance',
            80
        );
    }

    /**
     * Load the shared Admin UI on the Example module page.
     */
    public function shouldLoadAdminUi(
        bool $shouldEnqueue,
        string $hookSuffix
    ): bool {
        if (
            $this->pageHook !== ''
            && $hookSuffix === $this->pageHook
        ) {
            return true;
        }

        return $shouldEnqueue;
    }
}