<?php

declare(strict_types=1);

namespace Goug\Framework\Core\AdminUi;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;

/**
 * Loads the shared GOUG administration interface assets.
 */
final class AdminAssetLoader
{
    /**
     * Framework configuration.
     */
    private Configuration $configuration;

    /**
     * Relative path to the compiled stylesheet.
     *
     * We may adjust this after wiring the Vite output.
     */
    private string $stylesheetPath;

    /**
     * Create the admin asset loader.
     */
    public function __construct(
        Configuration $configuration,
        string $stylesheetPath = 'assets/css/admin-ui.css'
    ) {
        $this->configuration = $configuration;
        $this->stylesheetPath = ltrim(
            $stylesheetPath,
            '/\\'
        );
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
     * Enqueue the shared admin stylesheet when requested.
     */
    public function enqueue(
        string $hookSuffix ): void {
        /**
         * Determine whether the shared GOUG Admin UI should load.
         *
         * Modules may opt their administration screens into the shared
         * design system without Core knowing about specific module pages.
         *
         * @param bool   $shouldEnqueue Whether the stylesheet should load.
         * @param string $hookSuffix    Current WordPress admin page hook.
         */
        $shouldEnqueue = (bool) apply_filters(
            'goug_framework_admin_ui_should_enqueue',
            false,
            $hookSuffix
        );

        if (! $shouldEnqueue) {
            return;
        }

        $absolutePath = $this->configuration->path(
            $this->stylesheetPath
        );

        if (! is_file($absolutePath)) {
            return;
        }

        wp_enqueue_style(
            'goug-admin-ui',
            $this->configuration->url(
                $this->stylesheetPath
            ),
            [],
            (string) filemtime($absolutePath)
        );
    }
}