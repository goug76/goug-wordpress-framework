<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Exceptions;

defined('ABSPATH') || exit;

use LogicException;

/**
 * Thrown when multiple modules use the same identifier.
 */
final class DuplicateModuleException extends LogicException
{
    /**
     * Create an exception for a duplicate module identifier.
     */
    public static function forId(string $moduleId): self
    {
        return new self(
            sprintf(
                'A module with the identifier "%s" is already registered.',
                $moduleId
            )
        );
    }
}