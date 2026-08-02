<?php

declare(strict_types=1);

namespace Goug\Framework\Core\Configuration;

defined('ABSPATH') || exit;

use InvalidArgumentException;

/**
 * Describes a GOUG Framework module.
 */
final class ModuleMetadata
{
    private string $id;

    private string $name;

    private string $description;

    private string $version;

    /**
     * Create module metadata.
     */
    public function __construct(
        string $id,
        string $name,
        string $description,
        string $version
    ) {
        $id = sanitize_key($id);
        $name = trim($name);
        $description = trim($description);
        $version = trim($version);

        if ($id === '') {
            throw new InvalidArgumentException(
                'The module identifier cannot be empty.'
            );
        }

        if ($name === '') {
            throw new InvalidArgumentException(
                'The module name cannot be empty.'
            );
        }

        if ($version === '') {
            throw new InvalidArgumentException(
                'The module version cannot be empty.'
            );
        }

        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function version(): string
    {
        return $this->version;
    }
}