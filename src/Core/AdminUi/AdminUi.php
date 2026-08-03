<?php

declare(strict_types=1);

namespace Goug\Framework\Core\AdminUi;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Runtime;

/**
 * Coordinates the shared GOUG administration interface foundation.
 */
final class AdminUi
{
    /**
     * Shared administration asset loader.
     */
    private ?AdminAssetLoader $assetLoader = null;

    /**
     * Register shared administration infrastructure.
     */
    public function register(Runtime $runtime): void
    {
        $this->assetLoader = new AdminAssetLoader(
            $runtime->configuration()
        );
    }

    /**
     * Activate shared administration behavior.
     */
    public function boot(): void
    {
        if ($this->assetLoader === null) {
            throw new \LogicException(
                'The shared Admin UI must be registered before it is booted.'
            );
        }

        $this->assetLoader->hooks();
    }
}