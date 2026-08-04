<?php

declare(strict_types=1);

namespace Goug\Framework\Modules\Dashboard\Registries;

defined('ABSPATH') || exit;

use Goug\Framework\Modules\Dashboard\Models\Panel;
use LogicException;

/**
 * Owns the collection of Dashboard panels.
 */
final class PanelRegistry
{
    /**
     * Registered panels indexed by identifier.
     *
     * @var array<string, Panel>
     */
    private array $panels = [];

    /**
     * Register one Dashboard panel.
     */
    public function register(Panel $panel): void
    {
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

    /**
     * Determine whether a panel is registered.
     */
    public function has(string $panelId): bool
    {
        return isset(
            $this->panels[$panelId]
        );
    }

    /**
     * Return one registered panel.
     */
    public function get(string $panelId): ?Panel
    {
        return $this->panels[$panelId] ?? null;
    }

    /**
     * Return all panels ordered by priority.
     *
     * @return list<Panel>
     */
    public function all(): array
    {
        $panels = array_values(
            $this->panels
        );

        usort(
            $panels,
            static function (
                Panel $first,
                Panel $second
            ): int {
                return $first->priority()
                    <=> $second->priority();
            }
        );

        return $panels;
    }
}