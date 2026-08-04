<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Models;

defined('ABSPATH') || exit;

use InvalidArgumentException;

/**
 * Represents one Dashboard panel.
 */
final class Panel
{
    private string $id;

    private string $title;

    private string $description;

    private string $viewPath;

    private int $priority;

    /**
     * Data made available to the panel view.
     *
     * @var array<string, mixed>
     */
    private array $viewData;

    /**
     * Create a Dashboard panel.
     *
     * @param array<string, mixed> $viewData
     */
    public function __construct(
        string $id,
        string $title,
        string $description,
        string $viewPath,
        int $priority = 10,
        array $viewData = []
    ) {
        $id = sanitize_key($id);
        $title = trim($title);
        $description = trim($description);
        $viewPath = trim($viewPath);

        if ($id === '') {
            throw new InvalidArgumentException(
                'The panel identifier cannot be empty.'
            );
        }

        if ($title === '') {
            throw new InvalidArgumentException(
                'The panel title cannot be empty.'
            );
        }

        if ($viewPath === '') {
            throw new InvalidArgumentException(
                'The panel view path cannot be empty.'
            );
        }

        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->viewPath = $viewPath;
        $this->priority = $priority;
        $this->viewData = $viewData;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function viewPath(): string
    {
        return $this->viewPath;
    }

    public function priority(): int
    {
        return $this->priority;
    }

    /**
     * @return array<string, mixed>
     */
    public function viewData(): array
    {
        return $this->viewData;
    }
}