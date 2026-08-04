<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Providers;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Runtime;
use Goug\Framework\Modules\Dashboard\Assets\DashboardAssetLoader;
use Goug\Framework\Modules\Dashboard\Controllers\DashboardController;
use Goug\Framework\Modules\Dashboard\Registries\PanelRegistry;
use Goug\Framework\Modules\Dashboard\Services\DashboardService;
use Goug\Framework\Modules\Dashboard\Panels\AtAGlancePanel;
use Goug\Framework\Modules\Dashboard\Services\ContentStatisticsService;
use LogicException;

/**
 * Wires the Dashboard module.
 */
final class DashboardProvider implements ProviderContract
{
    private ?DashboardController $controller = null;

    private ?DashboardAssetLoader $assetLoader = null;

    public function register(Runtime $runtime): void
    {
        $configuration = $runtime->configuration();

        $panelRegistry = new PanelRegistry();

        $panelRegistry->register(
            new AtAGlancePanel(
                new ContentStatisticsService(),
                $configuration->path(
                    'src/Modules/Dashboard/Views/Panels/at-a-glance.php'
                )
            )
        );

        $dashboardService = new DashboardService(
            $panelRegistry
        );

        $viewPath = $configuration->path(
            'src/Modules/Dashboard/Views/dashboard.php'
        );

        $this->controller = new DashboardController(
            $dashboardService,
            $viewPath
        );

        $this->assetLoader = new DashboardAssetLoader(
            $configuration
        );
    }

    public function boot(Runtime $runtime): void
    {
        if (
            $this->controller === null
            || $this->assetLoader === null
        ) {
            throw new LogicException(
                'The Dashboard module must be registered before it is booted.'
            );
        }

        $this->controller->hooks();
        $this->assetLoader->hooks();

        add_action(
            'goug_dashboard_page_registered',
            [$this->assetLoader, 'setPageHook']
        );
    }
}