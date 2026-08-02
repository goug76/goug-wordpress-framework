<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;
use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Modules\Dashboard\Providers\DashboardProvider;

/**
 * Describes the GOUG Dashboard module.
 */
final class Module implements ModuleContract
{
    /**
     * Module metadata.
     */
    private ModuleMetadata $metadata;

    /**
     * Create the Dashboard module.
     */
    public function __construct()
    {
        $this->metadata = new ModuleMetadata(
            'dashboard',
            'Dashboard',
            'Provides a modern, customizable WordPress dashboard.',
            '0.1.0'
        );
    }

    /**
     * Return descriptive module metadata.
     */
    public function metadata(): ModuleMetadata
    {
        return $this->metadata;
    }

    /**
     * Return the Dashboard provider classes.
     *
     * @return list<class-string<ProviderContract>>
     */
    public function providers(): array
    {
        return [
            DashboardProvider::class,
        ];
    }
}