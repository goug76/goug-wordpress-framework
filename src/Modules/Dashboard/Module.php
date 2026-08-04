<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;
use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Modules\Dashboard\Providers\DashboardProvider;

/**
 * Describes the GOUG Dashboard module.
 */
final class Module implements ModuleContract
{
    public function metadata(): ModuleMetadata
    {
        return new ModuleMetadata(
            'dashboard',
            'GOUG Dashboard',
            'Provides a modern administration dashboard.',
            '0.1.0'
        );
    }

    /**
     * @return list<class-string<ProviderContract>>
     */
    public function providers(): array
    {
        return [
            DashboardProvider::class,
        ];
    }
}