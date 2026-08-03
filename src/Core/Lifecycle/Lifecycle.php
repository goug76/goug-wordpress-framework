<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Lifecycle;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Registries\ModuleRegistry;
use Goug\Framework\Core\AdminUi\AdminUi;
use Goug\Framework\Core\ModuleDiscovery;
use Goug\Framework\Core\ModuleLoader;
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
    private ?AdminUi $adminUi = null;

    public function __construct( private readonly Runtime $runtime ) {

    }

    /**
     * Run the complete framework lifecycle.
     */
    public function run(): void
    {
        $this->prepare();
        $this->initialize();
        $this->bootCore();
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

        $this->runtime->setModuleDiscovery(
            new ModuleDiscovery()
        );

        $this->runtime->setModuleLoader(
            new ModuleLoader()
        );

        $this->adminUi = new AdminUi();
        $this->adminUi->register(
            $this->runtime
        );
    }

    private function bootCore(): void
    {
        if ($this->adminUi === null) {
            throw new \LogicException(
                'The shared Admin UI has not been initialized.'
            );
        }

        $this->adminUi->boot();
    }

    /**
     * Discover installed framework modules.
     */
    private function discoverModules(): void
    {
        $modules = $this->runtime
            ->moduleDiscovery()
            ->discover(
                $this->runtime->configuration()
            );

        $registry = $this->runtime->moduleRegistry();

        foreach ($modules as $module) {
            $registry->register(
                $module
            );
        }

        do_action(
            'goug_framework_modules_discovered',
            $registry,
            $this->runtime
        );
    }

    /**
     * Register discovered framework modules.
     */
    private function registerModules(): void
    {
        $loader = $this->runtime->moduleLoader();

        foreach (
            $this->runtime->moduleRegistry()->all()
            as $module
        ) {
            $loader->register(
                $module,
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
        $loader = $this->runtime->moduleLoader();

        foreach (
            $this->runtime->moduleRegistry()->all()
            as $module
        ) {
            $loader->boot(
                $module,
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