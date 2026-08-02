<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Contracts;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\ModuleMetadata;
use Goug\Framework\Core\Runtime;

/**
 * Defines the behavior required of every GOUG Framework module.
 */
interface ModuleContract
{
    /**
     * Return descriptive module metadata.
     */
    public function metadata(): ModuleMetadata;

    /**
     * Register the module's definitions and infrastructure.
     */
    public function register(Runtime $runtime): void;

    /**
     * Activate the module's runtime behavior.
     */
    public function boot(Runtime $runtime): void;
}