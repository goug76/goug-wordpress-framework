<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Contracts;

defined('ABSPATH') || exit;

/**
 * Defines a renderable Dashboard panel.
 */
interface PanelContract
{
    public function id(): string;

    public function title(): string;

    public function description(): string;

    public function viewPath(): string;

    public function priority(): int;

    /**
     * Return fresh data for the panel view.
     *
     * @return array<string, mixed>
     */
    public function viewData(): array;
}