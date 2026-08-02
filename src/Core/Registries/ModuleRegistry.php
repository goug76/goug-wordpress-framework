<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Registries;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Core\Exceptions\DuplicateModuleException;

/**
 * Owns the collection of registered GOUG Framework modules.
 *
 * The registry stores module instances for the current request. It does
 * not discover, register, boot, install, update, or remove modules.
 */
final class ModuleRegistry
{
    /**
     * Registered modules indexed by their stable identifiers.
     *
     * @var array<string, ModuleContract>
     */
    private array $modules = [];

    /**
     * Add a module to the registry.
     *
     * @throws DuplicateModuleException When the identifier already exists.
     */
    public function register(ModuleContract $module): void
    {
        $moduleId = $module->metadata()->id();

        if ($this->has($moduleId)) {
            throw DuplicateModuleException::forId(
                $moduleId
            );
        }

        $this->modules[$moduleId] = $module;
    }

    /**
     * Determine whether a module is registered.
     */
    public function has(string $moduleId): bool
    {
        return isset(
            $this->modules[$moduleId]
        );
    }

    /**
     * Return one registered module.
     */
    public function get(string $moduleId): ?ModuleContract
    {
        return $this->modules[$moduleId] ?? null;
    }

    /**
     * Return all registered modules.
     *
     * @return array<string, ModuleContract>
     */
    public function all(): array
    {
        return $this->modules;
    }
}