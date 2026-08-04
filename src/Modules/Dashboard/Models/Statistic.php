<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Models;

defined('ABSPATH') || exit;

use InvalidArgumentException;

/**
 * Represents one Dashboard statistic.
 */
final class Statistic
{
    public function __construct(
        private string $label,
        private int $value,
        private string $icon
    ) {
        $this->label = trim($this->label);
        $this->icon = trim($this->icon);

        if ($this->label === '') {
            throw new InvalidArgumentException(
                'The statistic label cannot be empty.'
            );
        }

        if ($this->value < 0) {
            throw new InvalidArgumentException(
                'The statistic value cannot be negative.'
            );
        }

        if ($this->icon === '') {
            throw new InvalidArgumentException(
                'The statistic icon cannot be empty.'
            );
        }
    }

    public function label(): string
    {
        return $this->label;
    }

    public function value(): int
    {
        return $this->value;
    }

    public function icon(): string
    {
        return $this->icon;
    }
}