<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Assets;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;

/**
 * Loads Dashboard-owned administration assets.
 */
final class DashboardAssetLoader
{
    private Configuration $configuration;

    private string $stylesheetPath;

    private string $pageHook = '';

    public function __construct(
        Configuration $configuration,
        string $stylesheetPath = 'assets/css/dashboard-css.css'
    ) {
        $this->configuration = $configuration;

        $this->stylesheetPath = ltrim(
            $stylesheetPath,
            '/\\'
        );
    }

    /**
     * Store the WordPress hook belonging to the Dashboard screen.
     */
    public function setPageHook(string $pageHook): void
    {
        $this->pageHook = $pageHook;
    }

    /**
     * Register WordPress hooks.
     */
    public function hooks(): void
    {
        add_action(
            'admin_enqueue_scripts',
            [$this, 'enqueue']
        );
    }

    /**
     * Load Dashboard assets only on the Dashboard screen.
     */
    public function enqueue(string $hookSuffix): void
    {
        if (
            $this->pageHook === ''
            || $hookSuffix !== $this->pageHook
        ) {
            return;
        }

        $absolutePath = $this->configuration->path(
            $this->stylesheetPath
        );

        if (! is_file($absolutePath)) {
            return;
        }

        wp_enqueue_style(
            'goug-dashboard',
            $this->configuration->url(
                $this->stylesheetPath
            ),
            ['goug-admin-ui'],
            (string) filemtime($absolutePath)
        );
    }
}