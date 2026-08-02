<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Contracts;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;

/**
 * Defines the descriptive requirements of a framework module.
 */
interface ModuleContract
{
    /**
     * Return descriptive module metadata.
     */
    public function metadata(): ModuleMetadata;

    /**
     * Return the provider classes owned by this module.
     *
     * @return list<class-string<ProviderContract>>
     */
    public function providers(): array;
}