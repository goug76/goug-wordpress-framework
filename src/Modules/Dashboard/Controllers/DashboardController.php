<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Controllers;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Services\DashboardService;
use RuntimeException;

/**
 * Coordinates the Dashboard administration screen.
 */
final class DashboardController
{
    private string $viewPath;

    private string $pageHook = '';

    private DashboardService $dashboardService;

    public function __construct(
        DashboardService $dashboardService,
        string $viewPath ) {
        $this->dashboardService = $dashboardService;
        $this->viewPath = $viewPath;
    }

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

    public function registerPage(): void
    {
        $this->pageHook = add_menu_page(
            'GOUG Dashboard',
            'GOUG Dashboard',
            'manage_options',
            'goug-dashboard',
            [$this, 'renderPage'],
            'dashicons-dashboard',
            2
        );

        /**
         * Fires after the Dashboard administration page is registered.
         *
         * @param string $pageHook WordPress administration page hook.
         */
        do_action(
            'goug_dashboard_page_registered',
            $this->pageHook
        );
    }

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

    public function renderPage(): void
    {
        if (! current_user_can('manage_options')) {
            return;
        }

        if (! is_file($this->viewPath)) {
            throw new RuntimeException(
                sprintf(
                    'The Dashboard view could not be found at "%s".',
                    $this->viewPath
                )
            );
        }

        $panels = $this->dashboardService->panels();

        require $this->viewPath;
    }
}