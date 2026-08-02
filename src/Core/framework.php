<?php

declare(strict_types=1);

namespace Goug\Framework\Core;

defined('ABSPATH') || exit;

use Goug\Framework\Core\Configuration\Configuration;

/**
 * Public entry point for GOUG Framework.
 */
final class Framework
{
    /**
     * Running framework application.
     */
    private static ?Application $application = null;

    /**
     * Prevent direct construction.
     */
    private function __construct()
    {
    }

    /**
     * Boot GOUG Framework.
     *
     * @param string $rootPath Absolute framework root path.
     * @param string $rootUrl  Public framework root URL.
     */
    public static function boot(
        string $rootPath,
        string $rootUrl
    ): void {
        if (self::$application !== null) {
            return;
        }

        $configuration = new Configuration(
            $rootPath,
            $rootUrl
        );

        self::$application = new Application(
            $configuration
        );

        self::$application->boot();
    }

    /**
     * Return the running application.
     */
    public static function application(): Application
    {
        if (self::$application === null) {
            throw new \RuntimeException(
                'GOUG Framework has not been booted.'
            );
        }

        return self::$application;
    }
}