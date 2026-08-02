<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;
use Goug\Framework\Core\Registries\ModuleRegistry;
use RuntimeException;

/**
 * Represents the running state of GOUG Framework.
 *
 * Runtime owns shared Core infrastructure for the lifetime of the
 * current WordPress request.
 */
final class Runtime
{
    /**
     * Framework installation configuration.
     */
    private Configuration $configuration;

    /**
     * Registered framework modules.
     */
    private ?ModuleRegistry $moduleRegistry = null;

    /**
     * Load framework modules.
     */
    private ?ModuleLoader $moduleLoader = null;

    /**
     * Create the framework runtime.
     */
    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    /**
     * Return the framework configuration.
     */
    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Store the module registry.
     */
    public function setModuleRegistry(
        ModuleRegistry $moduleRegistry
    ): void {
        if ($this->moduleRegistry !== null) {
            throw new RuntimeException(
                'The module registry has already been initialized.'
            );
        }

        $this->moduleRegistry = $moduleRegistry;
    }

    /**
     * Return the module registry.
     */
    public function moduleRegistry(): ModuleRegistry
    {
        if ($this->moduleRegistry === null) {
            throw new RuntimeException(
                'The module registry has not been initialized.'
            );
        }

        return $this->moduleRegistry;
    }

    /**
     * Store the module loader.
     */
    public function setModuleLoader(
        ModuleLoader $moduleLoader
    ): void {
        if ($this->moduleLoader !== null) {
            throw new \RuntimeException(
                'The module loader has already been initialized.'
            );
        }

        $this->moduleLoader = $moduleLoader;
    }

    /**
     * Return the module loader.
     */
    public function moduleLoader(): ModuleLoader
    {
        if ($this->moduleLoader === null) {
            throw new \RuntimeException(
                'The module loader has not been initialized.'
            );
        }

        return $this->moduleLoader;
    }
}