<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Registries;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Contracts\PanelContract;
use LogicException;

/**
 * Owns the collection of Dashboard panels.
 */
final class PanelRegistry
{
    /**
     * @var array<string, PanelContract>
     */
    private array $panels = [];

    public function register(
        PanelContract $panel
    ): void {
        $panelId = $panel->id();

        if ($this->has($panelId)) {
            throw new LogicException(
                sprintf(
                    'A Dashboard panel with the identifier "%s" is already registered.',
                    $panelId
                )
            );
        }

        $this->panels[$panelId] = $panel;
    }

    public function has(string $panelId): bool
    {
        return isset($this->panels[$panelId]);
    }

    public function get(
        string $panelId
    ): ?PanelContract {
        return $this->panels[$panelId] ?? null;
    }

    /**
     * @return list<PanelContract>
     */
    public function all(): array
    {
        $panels = array_values(
            $this->panels
        );

        usort(
            $panels,
            static fn (
                PanelContract $first,
                PanelContract $second
            ): int => $first->priority()
                <=> $second->priority()
        );

        return $panels;
    }
}