<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Example;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;
use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Core\Contracts\ProviderContract;
use Goug\Framework\Modules\Example\Providers\ExampleProvider;

/**
 * Describes the Example module.
 *
 * Replace "Example" and its metadata when creating a real module.
 */
final class Module implements ModuleContract
{
    /**
     * Return descriptive module metadata.
     */
    public function metadata(): ModuleMetadata
    {
        return new ModuleMetadata(
            'example',
            'Example',
            'Provides an example GOUG Framework module.',
            '0.1.0'
        );
    }

    /**
     * Return the providers owned by this module.
     *
     * @return list<class-string<ProviderContract>>
     */
    public function providers(): array
    {
        return [
            ExampleProvider::class,
        ];
    }
}