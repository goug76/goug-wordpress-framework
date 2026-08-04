<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Example\Services;

defined('ABSPATH') || exit;

/**
 * Provides Example module business logic.
 */
final class ExampleService
{
    /**
     * Return the example notice heading.
     */
    public function heading(): string
    {
        return 'GOUG Framework Example Module';
    }

    /**
     * Return the example notice message.
     */
    public function message(): string
    {
        return 'The complete module lifecycle is working: discovery, registration, booting, service execution, controller coordination, and view rendering.';
    }
}