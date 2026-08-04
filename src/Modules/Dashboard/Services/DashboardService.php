<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Services;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Models\Panel;
use Goug\Framework\Modules\Dashboard\Registries\PanelRegistry;

/**
 * Provides Dashboard page data.
 */
final class DashboardService
{
    private PanelRegistry $panelRegistry;

    public function __construct(
        PanelRegistry $panelRegistry
    ) {
        $this->panelRegistry = $panelRegistry;
    }

    /**
     * Return the panels available to the current Dashboard.
     *
     * @return list<Panel>
     */
    public function panels(): array
    {
        return $this->panelRegistry->all();
    }
}