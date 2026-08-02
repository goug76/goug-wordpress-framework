<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Core\Contracts\ProviderContract;
use LogicException;

/**
 * Loads and executes module providers.
 *
 * The loader owns provider construction and lifecycle execution.
 * Modules only describe which providers belong to them.
 */
final class ModuleLoader
{
    /**
     * Provider instances indexed by module identifier.
     *
     * @var array<string, list<ProviderContract>>
     */
    private array $providers = [];

    /**
     * Register all providers belonging to a module.
     */
    public function register(
        ModuleContract $module,
        Runtime $runtime
    ): void {
        $moduleId = $module->metadata()->id();

        if (isset($this->providers[$moduleId])) {
            throw new LogicException(
                sprintf(
                    'Providers for module "%s" have already been registered.',
                    $moduleId
                )
            );
        }

        $this->providers[$moduleId] = $this->createProviders(
            $module
        );

        foreach ($this->providers[$moduleId] as $provider) {
            $provider->register($runtime);
        }
    }

    /**
     * Boot all providers belonging to a module.
     */
    public function boot(
        ModuleContract $module,
        Runtime $runtime
    ): void {
        $moduleId = $module->metadata()->id();

        if (! isset($this->providers[$moduleId])) {
            throw new LogicException(
                sprintf(
                    'Providers for module "%s" have not been registered.',
                    $moduleId
                )
            );
        }

        foreach ($this->providers[$moduleId] as $provider) {
            $provider->boot($runtime);
        }
    }

    /**
     * Create the provider instances declared by a module.
     *
     * @return list<ProviderContract>
     */
    private function createProviders(
        ModuleContract $module
    ): array {
        $providers = [];

        foreach ($module->providers() as $providerClass) {
            if (! is_a(
                $providerClass,
                ProviderContract::class,
                true
            )) {
                throw new LogicException(
                    sprintf(
                        'Provider "%s" must implement %s.',
                        $providerClass,
                        ProviderContract::class
                    )
                );
            }

            $provider = new $providerClass();

            if (! $provider instanceof ProviderContract) {
                throw new LogicException(
                    sprintf(
                        'Provider "%s" could not be created.',
                        $providerClass
                    )
                );
            }

            $providers[] = $provider;
        }

        return $providers;
    }
}