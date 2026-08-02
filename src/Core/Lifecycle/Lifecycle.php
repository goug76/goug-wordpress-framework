<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Lifecycle;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Registries\ModuleRegistry;
use Goug\Framework\Modules\Dashboard\Module as DashboardModule;
use Goug\Framework\Core\Runtime;

/**
 * Coordinates the GOUG Framework lifecycle.
 *
 * Each lifecycle phase runs in a predictable order. The lifecycle
 * coordinates initialization but does not own the infrastructure or
 * feature behavior introduced during those phases.
 */
final class Lifecycle
{
    public function __construct( private readonly Runtime $runtime ) {

    }

    /**
     * Run the complete framework lifecycle.
     */
    public function run(): void
    {
        $this->prepare();
        $this->initialize();
        $this->discoverModules();
        $this->registerModules();
        $this->bootModules();
        $this->ready();
    }

    /**
     * Prepare the framework environment.
     */
    private function prepare(): void
    {
        // Preparation logic will be introduced when required.
    }

    /**
     * Initialize shared Core infrastructure.
     */
    private function initialize(): void
    {
        $this->runtime->setModuleRegistry(
            new ModuleRegistry()
        );
    }

    /**
     * Discover installed framework modules.
     */
    private function discoverModules(): void
    {
        $this->runtime
            ->moduleRegistry()
            ->register(
                new DashboardModule()
            );
    }

    /**
     * Register discovered framework modules.
     */
    private function registerModules(): void
    {
        foreach (
            $this->runtime->moduleRegistry()->all()
            as $module
        ) {
            $module->register(
                $this->runtime
            );
        }

        do_action(
            'goug_framework_modules_registered',
            $this->runtime->moduleRegistry(),
            $this->runtime
        );
    }

    /**
     * Boot registered framework modules.
     */
    private function bootModules(): void
    {
        foreach (
            $this->runtime->moduleRegistry()->all()
            as $module
        ) {
            $module->boot(
                $this->runtime
            );
        }

        do_action(
            'goug_framework_modules_booted',
            $this->runtime->moduleRegistry(),
            $this->runtime
        );
    }

    /**
     * Mark the framework as ready.
     */
    private function ready(): void
    {
        /**
         * Fires after GOUG Framework Core and every discovered module
         * have completed registration and booting.
         *
         * @param Runtime $runtime Current framework runtime.
         */
        do_action(
            'goug_framework_ready',
            $this->runtime
        );
    }
}