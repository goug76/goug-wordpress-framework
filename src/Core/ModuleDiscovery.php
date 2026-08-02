<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use FilesystemIterator;
use Goug\Framework\Core\Configuration\Configuration;
use Goug\Framework\Core\Contracts\ModuleContract;
use LogicException;

/**
 * Discovers installed GOUG Framework modules.
 *
 * A valid module must follow this convention:
 *
 * src/Modules/<ModuleName>/Module.php
 *
 * with the corresponding class:
 *
 * Goug\Framework\Modules\<ModuleName>\Module
 */
final class ModuleDiscovery
{
    private const MODULE_NAMESPACE = 'Goug\\Framework\\Modules\\';

    /**
     * Discover all installed framework modules.
     *
     * @return list<ModuleContract>
     */
    public function discover(
        Configuration $configuration
    ): array {
        $modulesPath = $configuration->path(
            'src/Modules'
        );

        if (! is_dir($modulesPath)) {
            return [];
        }

        $moduleDirectories = $this->moduleDirectories(
            $modulesPath
        );

        $modules = [];

        foreach ($moduleDirectories as $moduleDirectory) {
            $module = $this->createModule(
                $moduleDirectory
            );

            if ($module !== null) {
                $modules[] = $module;
            }
        }

        return $modules;
    }

    /**
     * Return module directories in a predictable order.
     *
     * @return list<string>
     */
    private function moduleDirectories(
        string $modulesPath
    ): array {
        $directories = [];

        $iterator = new FilesystemIterator(
            $modulesPath,
            FilesystemIterator::SKIP_DOTS
        );

        foreach ($iterator as $item) {
            if (! $item->isDir()) {
                continue;
            }

            $directories[] = $item->getPathname();
        }

        sort(
            $directories,
            SORT_NATURAL | SORT_FLAG_CASE
        );

        return $directories;
    }

    /**
     * Create a module from one discovered directory.
     */
    private function createModule(
        string $moduleDirectory
    ): ?ModuleContract {
        $entryPoint = $moduleDirectory
            . DIRECTORY_SEPARATOR
            . 'Module.php';

        if (! is_file($entryPoint)) {
            return null;
        }

        $directoryName = basename(
            $moduleDirectory
        );

        $moduleClass = self::MODULE_NAMESPACE
            . $directoryName
            . '\\Module';

        if (! class_exists($moduleClass)) {
            throw new LogicException(
                sprintf(
                    'The module entry point "%s" exists, but class "%s" could not be loaded.',
                    $entryPoint,
                    $moduleClass
                )
            );
        }

        if (! is_a(
            $moduleClass,
            ModuleContract::class,
            true
        )) {
            throw new LogicException(
                sprintf(
                    'Module class "%s" must implement %s.',
                    $moduleClass,
                    ModuleContract::class
                )
            );
        }

        $module = new $moduleClass();

        if (! $module instanceof ModuleContract) {
            throw new LogicException(
                sprintf(
                    'Module class "%s" could not be initialized.',
                    $moduleClass
                )
            );
        }

        return $module;
    }
}