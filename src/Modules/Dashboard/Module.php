<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;
use Goug\Framework\Core\Contracts\ModuleContract;
use Goug\Framework\Core\Runtime;

/**
 * Provides the GOUG Dashboard module.
 *
 * The module coordinates Dashboard responsibilities but does not
 * implement individual Dashboard features directly.
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
     * Register Dashboard definitions and infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        // Dashboard registration will be introduced when required.
    }

    /**
     * Activate Dashboard runtime behavior.
     */
    public function boot(Runtime $runtime): void
    {
        if (
            defined('WP_DEBUG') &&
            WP_DEBUG
        ) {
            error_log(
                sprintf(
                    'GOUG module booted: %s %s',
                    $this->metadata()->name(),
                    $this->metadata()->version()
                )
            );
        }
    }
}