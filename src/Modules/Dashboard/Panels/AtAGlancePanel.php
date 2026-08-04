<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Panels;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Contracts\PanelContract;
use Goug\Framework\Modules\Dashboard\Services\AtAGlanceService;

/**
 * Provides the Dashboard At a Glance panel.
 */
final class AtAGlancePanel implements PanelContract
{
    public function __construct(
        private AtAGlanceService $service,
        private string $viewPath
    ) {
    }

    public function id(): string
    {
        return 'at-a-glance';
    }

    public function title(): string
    {
        return 'At a Glance';
    }

    public function description(): string
    {
        return 'A quick summary of your published website content.';
    }

    public function viewPath(): string
    {
        return $this->viewPath;
    }

    public function priority(): int
    {
        return 10;
    }

    /**
     * @return array<string, mixed>
     */
    public function viewData(): array
    {
        return [
            'contentCounts' => $this->service
                ->contentCounts(),
        ];
    }
}